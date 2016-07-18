<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Modelo de métricas.
 *
 * @MongoDB\EmbeddedDocument
 */
class Metrics
{
    /**
     * @MongoDB\Int
     * @MongoDB\Index
     */
    protected $averageRating;

    /**
     * @MongoDB\Int
     * @MongoDB\Index
     */
    protected $projectsInvited;

    /**
     * @MongoDB\Int
     */
    protected $projectsAccepted;

    /**
     * @MongoDB\Int
     */
    protected $projectsCompleted;

    /**
     * @MongoDB\Int
     */
    protected $providersUsed;

    /**
     * @param null|Rating $rating
     * @param null|int    $invited
     * @param null|int    $accepted
     * @param null|int    $completed
     */
    public function __construct($rating = null, $invited = 0, $accepted = 0, $completed = 0)
    {
        $this->setAverageRating($rating);
        $this->setProjectsInvited($invited);
        $this->setProjectsAccepted($accepted);
        $this->setProjectsCompleted($completed);
    }

    /**
     * @param int $averageRating
     * @return self
     */
    public function setAverageRating($averageRating)
    {
        $this->averageRating = $averageRating;

        return $this;
    }

    /**
     * @return int
     */
    public function getAverageRating()
    {
        return $this->averageRating;
    }

    /**
     * @param int $projectsInvited
     * @return self
     */
    public function setProjectsInvited($projectsInvited)
    {
        $this->projectsInvited = $projectsInvited;

        return $this;
    }

    /**
     * @return int
     */
    public function getProjectsInvited()
    {
        return $this->projectsInvited;
    }

    /**
     * @param int $projectsAccepted
     * @return self
     */
    public function setProjectsAccepted($projectsAccepted)
    {
        $this->projectsAccepted = $projectsAccepted;

        return $this;
    }

    /**
     * @return int
     */
    public function getProjectsAccepted()
    {
        return $this->projectsAccepted;
    }

    /**
     * @param int $projectsCompleted
     * @return self
     */
    public function setProjectsCompleted($projectsCompleted)
    {
        $this->projectsCompleted = $projectsCompleted;

        return $this;
    }

    /**
     * @return int
     */
    public function getProjectsCompleted()
    {
        return $this->projectsCompleted;
    }

    /**
     * @param int $providersUsed
     * @return self
     */
    public function setProvidersUsed($providersUsed)
    {
        $this->providersUsed = $providersUsed;

        return $this;
    }

    /**
     * @return int
     */
    public function getProvidersUsed()
    {
        return $this->providersUsed;
    }
}
