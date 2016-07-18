<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Valoración del trabajo de un proveedor en un proyecto.
 *
 * @MongoDB\Document(
 *     collection="ratings",
 *     repositoryClass="Persistence\Repository\RatingRepository"
 * )
 */
class Rating implements DocumentInterface
{
    use Properties\IdentityTrait;
    use Properties\AuthorTrait;
    use Properties\NotesTrait;
    use Properties\TimestampTrait;
    use Properties\ProviderTrait;
    use Properties\ProjectTrait;

    /**
     * @MongoDB\EmbedMany(targetDocument="Persistence\Model\CompetenceRating")
     */
    protected $competences;

    /**
     * @param null|array    $competences
     * @param null|Project  $project
     * @param null|Provider $provider
     * @param null|User     $user
     */
    public function __construct($competences = null, $project = null, $provider = null, $user = null)
    {
        $this->setCompetences($competences);
        $this->setProject($project);
        $this->setProvider($provider);
        $this->setAuthor($user);
    }

    /**
     * @param array $competences
     * @return self
     */
    public function setCompetences($competences)
    {
        $this->competences = $competences;

        return $this;
    }

    /**
     * @return array
     */
    public function getCompetences()
    {
        return $this->competences;
    }
}
