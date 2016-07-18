<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Registra una colección de ficheros.
 */
trait ContactTrait
{
    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Contact")
     */
    protected $contact;

    /**
     * @param null|Contact $contact
     *
     * @return self
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * @return Persistence\Model\Contact
     */
    public function getContact()
    {
        return $this->contact;
    }
}
