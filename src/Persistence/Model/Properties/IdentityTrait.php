<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Identifica de forma única a un modelo
 */
trait IdentityTrait
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @param string $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
