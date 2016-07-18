<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Determina el nombre de una persona
 */
trait PersonTrait
{
    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $firstName;

    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $lastName;

    /**
     * @return string
     */
    public function getFullName()
    {
        return implode(' ', [$this->firstName, $this->lastName]);
    }

    /**
     * @param string $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}
