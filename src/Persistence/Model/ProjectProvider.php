<?php

namespace Persistence\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Proveeder para un proyecto.
 *
 * @MongoDB\EmbeddedDocument
 */
class ProjectProvider
{
    use Properties\ProviderTrait;
    use Properties\StatusTrait;

    const INVITED = 'invited';
    const REJECTED = 'rejected';
    const ACCEPTED = 'accepted';
    const BUDGETED = 'budgeted';
    const ASSIGNED = 'assigned';

    /**
     * @MongoDB\Date
     */
    protected $assignation;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Rejection")
     */
    protected $rejection;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Budget")
     */
    protected $budget;

    /**
     * @param Provider $provider
     */
    public function __construct($provider = null)
    {
        $this->setStatus(self::INVITED);
        $this->setProvider($provider);
    }

    /**
     * @param DateTime $assignation
     * @return self
     */
    public function setAssignation($assignation)
    {
        $this->assignation = $assignation;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getAssignation()
    {
        return $this->assignation;
    }

    /**
     * @return boolean
     */
    public function hasAssignation()
    {
        return $this->getAssignation() instanceof \DateTime;
    }

    /**
     * @param Persistence\Model\Rejection $rejection
     * @return self
     */
    public function setRejection(Rejection $rejection = null)
    {
        $this->rejection = $rejection;

        return $this;
    }

    /**
     * @return Persistence\Model\Rejection
     */
    public function getRejection()
    {
        return $this->rejection;
    }

    /**
     * @param Persistence\Model\Budget $budget
     * @return self
     */
    public function setBudget(Budget $budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * @return Persistence\Model\Budget
     */
    public function getBudget()
    {
        return $this->budget;
    }
}
