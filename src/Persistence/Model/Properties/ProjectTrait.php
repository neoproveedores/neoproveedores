<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Registra un proyecto
 */
trait ProjectTrait
{
    /**
     * @MongoDB\ReferenceOne(targetDocument="Persistence\Model\Project")
     * @MongoDB\Index
     */
    protected $project;

    /**
     * @param null|Project $project
     * @return self
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }
}
