<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\User;

/**
 * Determina el autor de un modelo
 */
trait AuthorTrait
{
    /**
     * @MongoDB\ReferenceOne(targetDocument="Persistence\Model\User")
     */
    protected $author;

    /**
     * @param null|User $author
     * @return self
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
