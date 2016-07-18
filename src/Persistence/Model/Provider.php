<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use AppBundle\SearchEngine\Indexer\ProviderIndexer;

/**
 * Modelo de provedor.
 *
 * @MongoDB\Document(
 *     collection="providers",
 *     repositoryClass="Persistence\Repository\ProviderRepository"
 * )
 * @MongoDB\HasLifecycleCallbacks
 */
class Provider implements DocumentInterface
{
    use Properties\UserTrait;
    use Properties\ContactTrait;
    use Properties\HourRateTrait;
    use Properties\IdentityTrait;
    use Properties\KindTrait;
    use Properties\MetricsTrait;
    use Properties\NotesTrait;
    use Properties\StatusTrait;
    use Properties\TimestampTrait;
    use Properties\TimingTrait;
    use Properties\KeywordsTrait;

    const FREELANCE = 'freelance';
    const COMPANY = 'company';

    const DRAFT = 'draft';
    const ACTIVE = 'active';
    const UNAVAILABLE = 'unavailable';

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Address")
     */
    protected $address;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Billing")
     */
    protected $billing;

    /**
     * @MongoDB\EmbedMany(targetDocument="Persistence\Model\Contact")
     */
    protected $contacts;

    /**
     * @MongoDB\EmbedMany(targetDocument="Persistence\Model\Skill")
     */
    protected $skills;

    /**
     * @MongoDB\Boolean
     */
    protected $skillsClosed;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Persistence\Model\Project")
     */
    protected $projects;

    /**
     * Propiedad automática: sólo para los resultados de búsqueda
     * @MongoDB\String
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $freelanceContact;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this
            ->setKind(self::FREELANCE)
            ->setStatus(self::DRAFT)
            ->setSkills([])
            ->setContact(new Contact())
            ->setAddress(new Address())
            ->setBilling(new Billing())
            ->setMetrics(new Metrics())
        ;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContact() ? (string) $this->getContact() : '';
    }


    /**
     * Clona los documentos incrustados.
     * Incompleto: al menos faltaria clonar las collecciones de documentos
     * incrustados.
     */
    public function __clone()
    {
        $embeds = [
            'address',
            'billing',
        ];

        foreach ($embeds as $embed) {
            if (is_object($this->$embed)) {
                $this->$embed = clone $this->$embed;
            }
        }
    }

    /**
     * @param Persistence\Model\Address $address
     *
     * @return self
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Persistence\Model\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Persistence\Model\Billing $billing
     *
     * @return self
     */
    public function setBilling(Billing$billing)
    {
        $this->billing = $billing;

        return $this;
    }

    /**
     * @return Persistence\Model\Billing
     */
    public function getBilling()
    {
        return $this->billing;
    }

    /**
     * @param array $contacts
     *
     * @return self
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * @return array
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param array $skills
     *
     * @return self
     */
    public function setSkills($skills)
    {
        $this->skills = $skills;

        return $this;
    }

    /**
     * @return array
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * @param string $ability
     *
     * @return Skill
     */
    public function getSkill($ability)
    {
        foreach ($this->getSkills() as $skill) {
            if ($skill->getAbility()->getName() == $ability) {
                return $skill;
            }
        }
    }

    /**
     * @param boolean $closed
     *
     * @return self
     */
    public function setSkillsClosed($closed)
    {
        $this->skillsClosed = $closed;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasSkillsClosed()
    {
        return $this->skillsClosed;
    }

    /**
     * @param array $projects
     *
     * @return self
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * @return array
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param array $ratings
     *
     * @return self
     */
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;

        return $this;
    }

    /**
     * @return array
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param array $ratings
     *
     * @return array
     */
    public function getCompetencesRatings($ratings = null)
    {
        $competences = [];
        $ratings = array_reverse($ratings);

        foreach ($ratings as $rating) {
            foreach ($rating->getCompetences() as $competence) {
                $name = $competence->getCompetence()->getName();

                if (!isset($competences[$name])) {
                    $competences[$name] = [];
                }

                $competences[$name][] = $competence->getRating();
            }
        }

        return $competences;
    }

    /**
     * @param array $ratings
     *
     * @return array
     */
    public function getCompetencesRatingsAverage($ratings = null)
    {
        $averages = [];

        foreach ($ratings as $rating) {
            $competences = [];
            foreach ($rating->getCompetences() as $competence) {
                $competences[] = $competence->getRating();
            }
            $averages[] = round(array_sum($competences) / count($competences));
        }

        return $averages;
    }

    /**
     * @param  string $size
     * @return string
     */
    public function getAvatar($size = '')
    {
        if ($this->contact) {
            return $this->contact->getAvatarImage($size);
        }
    }

    /**
     * @param  boolean $value
     * @return self
     */
    public function setFreelanceContact($value)
    {
        $this->freelanceContact = $value;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasFreelanceContact()
    {
        if (! $this->getContacts() || ! $this->getContacts()->count()) {
            return is_object($this->getContact());
        }

        return false;
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
                return $this == $user->getProvider();
            }
        }

        return false;
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validateName(ExecutionContextInterface $context)
    {
        $message = (new NotBlank())->message;

        if ($contact = $this->getContact()) {
            if ($this->getKind() == self::FREELANCE) {
                if (empty($contact->getFirstName())) {
                    $context->addViolationAt('contact.firstName', $message);
                }

                if (empty($contact->getLastName())) {
                    $context->addViolationAt('contact.lastName', $message);
                }
            } else if ($this->getKind() == self::COMPANY) {
                if (empty($contact->getBusinessName())) {
                    $context->addViolationAt('contact.businessName', $message);
                }
            }
        }
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validateContacts(ExecutionContextInterface $context)
    {
        $message = 'Añade al menos un contacto';

        if ($this->getKind() == self::COMPANY) {
            if (! $this->getContacts() || ! $this->getContacts()->count()) {
                $context->addViolationAt('contacts', $message);
            }
        }

        if ($this->getKind() == self::FREELANCE) {
            if ($this->freelanceContact === false) {
                $message .= ' o marca la opción de usar el propio autónomo';

                if (! $this->getContacts() || ! $this->getContacts()->count()) {
                    $context->addViolationAt('contacts', $message);
                }
            }
        }
    }

    /**
     * @MongoDB\PrePersist
     */
    public function updateName()
    {
        $this->name = (string) $this->contact;

        if ($this->user instanceof User) {
            $this->user->setName($this->name);
        }
    }

    /**
     * Actualiza las palabras clave
     *
     * @MongoDB\PreFlush
     */
    public function updateKeywords()
    {
        ProviderIndexer::ensureKeywords($this);
    }

    /**
     * @param  Projet $project
     * @return int
     */
    public function getWeight(Project $project)
    {
        $weight = $this->getMetrics()->getAverageRating() * 10;

        foreach ($project->getAbilities() as $ability) {
            if ($skill = $this->getSkill($ability->getName())) {
                if (! $skill->isBanned()) {
                    $weight += 100;
                }
            }
        }

        return $weight;
    }
}
