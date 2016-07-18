<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Valoración del la competencia en un trabajo de un proveedor.
 *
 * @MongoDB\EmbeddedDocument
 */
class CompetenceRating
{
    use Properties\RatingTrait;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Persistence\Model\Competence")
     */
    protected $competence;

    /**
     * @param Competence $competence
     * @param int        $rating
     */
    public function __construct($competence = null, $rating = null)
    {
        $this->setCompetence($competence);
        $this->setRating($rating);
    }

    /**
     * @param Competence $competence
     *
     * @return self
     */
    public function setCompetence(Competence $competence)
    {
        $this->competence = $competence;

        return $this;
    }

    /**
     * @return Competence
     */
    public function getCompetence()
    {
        return $this->competence;
    }
}
