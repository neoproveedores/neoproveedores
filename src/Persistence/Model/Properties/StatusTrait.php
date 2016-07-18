<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Determina el estado.
 */
trait StatusTrait
{
    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $status;

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (is_string($status)) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * @return self
     */
    public function resetStatus()
    {
        $this->status = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string|array $options
     *
     * @return bool
     */
    public function hasStatus($options)
    {
        $options = is_array($options) ? $options : [$options];

        return in_array($this->getStatus(), $options);
    }
}
