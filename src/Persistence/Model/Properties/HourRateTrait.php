<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\Amount;

/**
 * Registra una tarifa por hora
 */
trait HourRateTrait
{
    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Amount")
     */
    protected $hourRate;

    /**
     * @param null|Amount $hourRate
     * @return self
     */
    public function setHourRate($hourRate)
    {
        $this->hourRate = $hourRate;

        return $this;
    }

    /**
     * @return Persistence\Model\Amount
     */
    public function getHourRate()
    {
        return $this->hourRate;
    }
}
