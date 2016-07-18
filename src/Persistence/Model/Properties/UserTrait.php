<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\User;

/**
 * Determina el usuario de un modelo
 */
trait UserTrait
{
    /**
     * @MongoDB\ReferenceOne(targetDocument="Persistence\Model\User")
     */
    protected $user;

    /**
     * @param null|User $user
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
