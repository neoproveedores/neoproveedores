<?php

namespace Persistence\Factory;

use Persistence\Model\User;
use Persistence\Model\Project;
use Persistence\Model\Provider;
use Persistence\Model\Rating;
use Persistence\Model\CompetenceRating;

/**
 * Crea valoraciones para el pueblo llano.
 */
class RatingFactory
{
    /**
     * @var Persistence\Repository\CompetenceRepository
     */
    protected $competenceRepository;

    /**
     * @param CompetenceRepository $competenceRepository
     */
    public function __construct($competenceRepository)
    {
        $this->competenceRepository = $competenceRepository;
    }

    /**
     * @param Project  $project
     * @param Provider $provider
     * @param User     $user
     *
     * @return Rating
     */
    public function createEmpty(Project $project, Provider $provider, User $user)
    {
        $competences = [];

        foreach ($this->competenceRepository->findAll() as $competence) {
            $competences[] = new CompetenceRating($competence);
        }

        return new Rating($competences, $project, $provider, $user);
    }
}
