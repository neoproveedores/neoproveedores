<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Intervalo temporal.
 *
 * @MongoDB\EmbeddedDocument
 */
class Timing
{
    /**
     * @MongoDB\Date
     */
    protected $start;

    /**
     * @MongoDB\Date
     */
    protected $end;

    /**
     * @param DateTime $start
     * @param DateTime $end
     */
    public function __construct($start = null, $end = null)
    {
        $this->setStart($start);
        $this->setEnd($end);
    }

    /**
     * @param DateTime $start
     * @return self
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param DateTime $end
     * @return self
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }
}
