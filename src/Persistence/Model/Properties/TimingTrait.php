<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\Timing;

/**
 * Determina un intervalo de tiempo
 */
trait TimingTrait
{
    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Timing")
     */
    protected $timing;

    /**
     * @param Persistence\Model\Timing $timing
     * @return self
     */
    public function setTiming(Timing $timing)
    {
        $this->timing = $timing;

        return $this;
    }

    /**
     * @return Persistence\Model\Timing
     */
    public function getTiming()
    {
        return $this->timing;
    }
}
