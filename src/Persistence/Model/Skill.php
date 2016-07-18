<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Aptitud de un proveedor en una habilidad.
 *
 * @MongoDB\EmbeddedDocument
 */
class Skill
{
    use Properties\HourRateTrait;
    use Properties\MetricsTrait;
    use Properties\NotesTrait;
    use Properties\RatingTrait;
    use Properties\FilesTrait;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Persistence\Model\Ability",
     *     cascade={"persist"}
     * )
     * @MongoDB\Index
     */
    protected $ability;

    /**
     * @MongoDB\Boolean
     */
    protected $banned;

    /**
     * @param null|Ability $ability
     * @param float        $rate    Hour rate amount
     * @param int          $rating
     */
    public function __construct($ability = null, $rate = null, $rating = null)
    {
        $this
            ->setAbility($ability)
            ->setHourRate(new Amount($rate))
            ->setRating($rating)
            ->setMetrics(new Metrics())
        ;
    }

    /**
     * @param null|Ability $ability
     *
     * @return self
     */
    public function setAbility($ability)
    {
        $this->ability = $ability;

        return $this;
    }

    /**
     * @return Ability
     */
    public function getAbility()
    {
        return $this->ability;
    }

    /**
     * @param boolean $banned
     *
     * @return self
     */
    public function setBanned($banned)
    {
        $this->banned = $banned;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBanned()
    {
        return $this->banned;
    }
}
