<?php

namespace Persistence\Model;

use FOS\UserBundle\Model as FOSUserBundle;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\DocumentInterface;

/**
 * Modelo de usuario.
 *
 * @MongoDB\Document(
 *     collection="users",
 *     repositoryClass="Persistence\Repository\UserRepository"
 * )
 * @MongoDB\HasLifecycleCallbacks
 */
class User extends FOSUserBundle\User implements DocumentInterface
{
    use Properties\ProviderTrait;
    use Properties\ContactTrait;
    use Properties\TimestampTrait;

    const MANAGER = 'manager';
    const PROVIDER = 'provider';

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Notifications")
     */
    protected $notifications;

    /**
     * @MongoDB\Date
     */
    protected $lastTimelineVisit;

    /**
     * @MongoDB\String
     */
    protected $disableNotificationsToken;

    /**
     * Propiedad automática: sólo para los resultados de búsqueda
     * @MongoDB\String
     */
    protected $name;

    /**
     * @var string
     */
    protected $role;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->notifications = new Notifications();
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->provider) {
            return (string) $this->provider->getContact();
        }

        return (string) $this->contact;
    }

    /**
     * @param String $username
     * @return self
     */
    public function setUsername($username)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        parent::setUsername($email);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        $mainRoles = ['ROLE_MANAGER', 'ROLE_PROJECT_MANAGER', 'ROLE_PROVIDER'];

        foreach ($this->getRoles() as $role) {
            if (in_array($role, $mainRoles)) {
                return $role;
            }
        }
    }

    /**
     * @param mixed $role
     * @return self
     */
    public function setRole($role)
    {
        $this->role = $role;
        $this->setRoles([$role]);

        return $this;
    }

    /**
     * @param Provider $provider
     * @return self
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        if ($provider instanceof Provider) {
            $this->provider->setUser($this);
        }

        return $this;
    }

    /**
     * @param null|Contact $contact
     * @return self
     */
    public function setContact(Contact $contact = null)
    {
        if ($this->provider) {
            $this->provider->setContact($contact);
        } else {
            $this->contact = $contact;
        }

        return $this;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        if ($this->provider) {
            return $this->provider->getContact();
        }

        return $this->contact;
    }

    /**
     * @param mixed $notifications
     * @return self
     */
    public function setNotifications(Notifications $notifications = null)
    {
        $this->notifications = $notifications;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param mixed $visit
     * @return self
     */
    public function setLastTimelineVisit($visit)
    {
        $this->lastTimelineVisit = $visit;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastTimelineVisit()
    {
        return $this->lastTimelineVisit;
    }

    /**
     * @param string $token
     * @return self
     */
    public function setDisableNotificationsToken($token)
    {
        $this->disableNotificationsToken = $token;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDisableNotificationsToken()
    {
        return $this->disableNotificationsToken;
    }

    /**
     * @MongoDB\PrePersist
     */
    public function updateName()
    {
        if (! $this->provider && $this->contact) {
            $this->name = (string) $this->contact;
        }
    }
}
