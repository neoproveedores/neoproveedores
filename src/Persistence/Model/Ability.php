<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\Metrics;

/**
 * Habilidad de un tipo.
 *
 * @MongoDB\Document(
 *     collection="abilities",
 *     repositoryClass="Persistence\Repository\AbilityRepository"
 * )
 */
class Ability implements DocumentInterface
{
    use Properties\IdentityTrait;
    use Properties\NameTrait;
    use Properties\MetricsTrait;

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
        $this->metrics = new Metrics();
    }
}
