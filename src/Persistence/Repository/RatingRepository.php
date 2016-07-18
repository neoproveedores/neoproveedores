<?php

namespace Persistence\Repository;

use Persistence\Model\Provider;
use Persistence\Model\Project;

/**
 * Repositorio para valoraciones.
 */
class RatingRepository extends AbstractDocumentRepository
{
    /**
     * @param Provider $provider
     * @param int      $limit
     *
     * @return mixed
     */
    public function findLastByProvider(Provider $provider, $limit = 7)
    {
        return $this
            ->createQueryBuilder()
            ->field('provider')->references($provider)
            ->sort('created', 'desc')
            ->limit($limit)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param Provider $provider
     */
    public function removeByProvider(Provider $provider)
    {
        $this
            ->createQueryBuilder()
            ->remove()
            ->field('provider')->references($provider)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param Project $project
     */
    public function removeByProject(Project $project)
    {
        $this
            ->createQueryBuilder()
            ->remove()
            ->field('project')->references($project)
            ->getQuery()
            ->execute()
        ;
    }
}
