<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Evento dentro de la aplicación.
 *
 * @MongoDB\Document(
 *     collection="events",
 *     repositoryClass="Persistence\Repository\EventRepository"
 * )
 */
class Event implements DocumentInterface
{
    use Properties\UserTrait;
    use Properties\IdentityTrait;
    use Properties\KindTrait;
    use Properties\ProjectTrait;
    use Properties\ProviderTrait;
    use Properties\TimestampTrait;

    /**
     * Cuando se crea un proveedor
     */
    const PROVIDER = 'provider';
    /**
     * Cuando se crea un proyecto
     */
    const PROJECT = 'project';
    /**
     * Cuando se manda una invitación
     */
    const INVITE = 'invite';
    /**
     * Cuando se acepta un proyecto
     */
    const ACCEPT = 'accept';
    /**
     * Cuando se rechaza un proyecto
     */
    const REJECT = 'reject';
    /**
     * Cuando se presupuesta un proyecto
     */
    const BUDGET = 'budget';
    /**
     * Cuando se modifica el presupuesto de un proyecto
     */
    const UPDATE_BUDGET = 'update_budget';
    /**
     * Cuando se envía un mensaje
     */
    const MESSAGE = 'message';

    /**
     * @MongoDB\ReferenceOne(targetDocument="Persistence\Model\Message")
     */
    protected $message;

    /**
     * @param string        $kind
     * @param null|User     $user
     * @param null|Project  $project
     * @param null|Provider $provider
     */
    public function __construct(
        $kind = null,
        $user = null,
        $project = null,
        $provider = null
    ) {
        $this
            ->setKind($kind)
            ->setUser($user)
            ->setProject($project)
            ->setProvider($provider)
        ;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        switch ($this->kind) {
            case self::PROVIDER:
                return sprintf(
                    '%s ha creado el proveedor %s',
                    $this->user->getName(),
                    $this->provider
                );
            case self::PROJECT:
                return sprintf(
                    '%s ha creado el proyecto %s',
                    $this->user->getName(),
                    $this->project
                );
            case self::ACCEPT:
                return sprintf(
                    '%s ha aceptado presupuestar %s',
                    $this->provider,
                    $this->project
                );
            case self::REJECT:
                return sprintf(
                    '%s ha rechazado presupuestar %s',
                    $this->provider,
                    $this->project
                );
            case self::BUDGET:
                return sprintf(
                    '%s ha presentado un presupuesto para %s',
                    $this->provider,
                    $this->project
                );
            case self::MESSAGE:
                return sprintf(
                    '%s ha añadido un nuevo mensaje en %s',
                    $this->user->getName(),
                    $this->project
                );
        }
    }

    /**
     * @return array
     */
    public static function getIcons()
    {
        return [
            self::PROVIDER => 'shipping',
            self::PROJECT => 'suitcase',
            self::ACCEPT => 'checkmark',
            self::REJECT => 'cancel',
            self::BUDGET => 'euro',
            self::MESSAGE => 'mail',
        ];
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return self::getIcons()[$this->kind];
    }

    /**
     * @param null|Message $message
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return null|Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
