<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Determina el nombre de algo
 */
trait NameTrait
{
    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $name;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? : '';
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
