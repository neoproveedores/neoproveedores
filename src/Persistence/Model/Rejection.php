<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Motivo de rechazo de un proyecto.
 *
 * @MongoDB\EmbeddedDocument
 */
class Rejection
{
    use Properties\NotesTrait;

    /**
     * @MongoDB\String
     */
    protected $reason;

    /**
     * @param string $reason
     * @return self
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
