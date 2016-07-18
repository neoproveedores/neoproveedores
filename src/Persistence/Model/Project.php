<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Persistence\Model\ProjectProvider;
use AppBundle\SearchEngine\Indexer\ProjectIndexer;

/**
 * Modelo de proyecto.
 *
 * @MongoDB\Document(
 *     collection="projects",
 *     repositoryClass="Persistence\Repository\ProjectRepository"
 * )
 * @MongoDB\HasLifecycleCallbacks
 */
class Project implements DocumentInterface
{
    use Properties\AuthorTrait;
    use Properties\FilesTrait;
    use Properties\IdentityTrait;
    use Properties\NameTrait;
    use Properties\StatusTrait;
    use Properties\TimestampTrait;
    use Properties\TimingTrait;
    use Properties\KeywordsTrait;

    /**
     * Borrador de proyecto
     */
    const DRAFT = 'draft';

    /**
     * Proyecto enviado a los proveedores
     */
    const SENT = 'sent';

    /**
     * Proyecto recibiendo presupuestos
     */
    const RECEIVING = 'receiving';

    /**
     * Proyecto asignado a un proveedor
     */
    const ASSIGNED = 'assigned';

    /**
     * Proyecto cerrado
     */
    const CLOSED = 'closed';

    /**
     * @MongoDB\String
     */
    protected $code;

    /**
     * @MongoDB\String
     */
    protected $description;

    /**
     * @MongoDB\String
     */
    protected $briefing;

    /**
     * @MongoDB\ReferenceMany(
     *     targetDocument="Persistence\Model\Ability",
     *     cascade={"persist"}
     * )
     */
    protected $abilities;

    /**
     * @MongoDB\EmbedMany(targetDocument="Persistence\Model\ProjectProvider")
     */
    protected $providers;

    /**
     * @MongoDB\ReferenceOne("Persistence\Model\Rating")
     */
    protected $rating;

    /**
     * Propiedad automática: sólo para ordenar los resultados de búsqueda
     * @MongoDB\Float
     * @MongoDB\Index
     */
    protected $budgetAmount;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setStatus(self::DRAFT);
    }

    /**
     * @param string $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $briefing
     *
     * @return self
     */
    public function setBriefing($briefing)
    {
        $this->briefing = $briefing;

        return $this;
    }

    /**
     * @return string
     */
    public function getBriefing()
    {
        return $this->briefing;
    }

    /**
     * @param array $abilities
     *
     * @return self
     */
    public function setAbilities($abilities)
    {
        $this->abilities = $abilities;

        return $this;
    }

    /**
     * @return array
     */
    public function getAbilities()
    {
        return $this->abilities;
    }

    /**
     * @param array $providers
     *
     * @return self
     */
    public function setProviders($providers)
    {
        $this->providers = $providers;

        return $this;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param ProjectProvider $provider
     *
     * @return self
     */
    public function addProjectProvider(ProjectProvider $provider)
    {
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * @param Provider $provider
     *
     * @return self
     */
    public function addProvider(Provider $provider)
    {
        $pp = $this->getProvider($provider);

        if ($pp) {
            $pp->setStatus(ProjectProvider::INVITED)->setRejection(null);
        } else {
            $this->providers[] = new ProjectProvider($provider);
        }

        return $this;
    }

    /**
     * @param Provider $provider
     *
     * @return ProjectProvider
     */
    public function getProvider(Provider $provider)
    {
        foreach ($this->getProviders() as $pr) {
            if ($provider->getId() == $pr->getProvider()->getId()) {
                return $pr;
            }
        }
    }

    /**
     * @param Provider $provider
     * @param string   $status
     *
     * @return boolean
     */
    public function hasProvider(Provider $provider, $status = null)
    {
        if ($provider = $this->getProvider($provider)) {
            if ($status) {
                return $provider->hasStatus($status);
            }

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllProviders()
    {
        $providers = [];

        foreach ($this->getProviders() as $provider) {
            $providers[] = $provider->getProvider();
        }

        return $providers;
    }

    /**
     * @return array
     */
    public function getProvidersIds()
    {
        $ids = [];

        foreach ($this->getProviders() as $provider) {
            $ids[] = $provider->getProvider()->getId();
        }

        return $ids;
    }

    /**
     * @return array
     */
    public function getProvidersWithBudget()
    {
        $providers = [];

        foreach ($this->getProviders() as $provider) {
            if ($provider->getBudget()) {
                $providers[] = $provider->getProvider();
            }
        }

        return $providers;
    }

    /**
     * @return Provider
     */
    public function getAssignedProvider()
    {
        foreach ($this->getProviders() as $pr) {
            if ($pr->hasStatus(ProjectProvider::ASSIGNED)) {
                return $pr->getProvider();
            }
        }
    }

    /**
     * @param Persistence\Model\Rating $rating
     *
     * @return self
     */
    public function setRating(Rating $rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Persistence\Model\Rating
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param null|Persistence\Model\Provider $provider
     *
     * @return null|Persistence\Model\Budget
     */
    public function getBudget($provider = null)
    {
        if (! $this->getProviders()) {
            return;
        }

        foreach ($this->getProviders() as $pr) {
            if ($provider) {
                if ($provider->getId() != $pr->getProvider()->getId()) {
                    continue;
                }
            } else {
                if (! $pr->hasAssignation()) {
                    continue;
                }
            }

            return $pr->getBudget();
        }
    }

    /**
     * @param  Provider $provider
     * @return self
     */
    public function removeProvider(Provider $provider)
    {
        foreach ($this->providers as $key => $pr) {
            if ($pr->getProvider()->getId() == $provider->getId()) {
                $this->providers->remove($key);
            }
        }

        return $this;
    }

    /**
     * @param mixed $user
     *
     * @return boolean
     */
    public function hasUser($user)
    {
        if ($user instanceof UserInterface) {
            if ($user->hasRole('ROLE_MANAGER')) {
                return true;
            }

            if ($user->hasRole('ROLE_PROVIDER')) {
                return $this->hasProvider($user->getProvider());
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isAssigned()
    {
        return $this->status == self::ASSIGNED;
    }

    /**
     * @return boolean
     */
    public function isEditable()
    {
        return $this->status != self::CLOSED;
    }

    /**
     * @return boolean
     */
    public function isRemovable()
    {
        return $this->isEditable();
    }

    /**
     * @MongoDB\PrePersist
     */
    public function updateBudgetAmount()
    {
        if ($budget = $this->getBudget()) {
            $this->budgetAmount = $budget->getAmount()->getValue();
        } else {
            $this->budgetAmount = null;
        }
    }

    /**
     * Actualiza las palabras clave
     *
     * @MongoDB\PreFlush
     */
    public function updateKeywords()
    {
        ProjectIndexer::ensureKeywords($this);
    }

    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::DRAFT,
            self::SENT,
            self::RECEIVING,
            self::ASSIGNED,
            self::CLOSED,
        ];
    }
}
