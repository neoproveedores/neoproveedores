<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Registra el tipo de documento.
 */
trait KindTrait
{
    /**
     * @MongoDB\String
     */
    protected $kind;

    /**
     * @param string $kind
     *
     * @return self
     */
    public function setKind($kind)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }
}
