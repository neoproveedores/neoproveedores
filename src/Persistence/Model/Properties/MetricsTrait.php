<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\Metrics;

/**
 * Registra unas métricas
 */
trait MetricsTrait
{
    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Metrics")
     */
    protected $metrics;

    /**
     * @param Persistence\Model\Metrics $metrics
     * @return self
     */
    public function setMetrics(Metrics $metrics)
    {
        $this->metrics = $metrics;

        return $this;
    }

    /**
     * @return Persistence\Model\Metrics
     */
    public function getMetrics()
    {
        return $this->metrics;
    }
}
