<?php

namespace Persistence\Repository;

use Persistence\Model\Project;
use Persistence\Model\Provider;

/**
 * Repositorio para proyectos.
 */
class ProjectRepository extends AbstractDocumentRepository
{
    /**
     * @return mixed
     */
    public function findPublic()
    {
        return $this
            ->createQueryBuilder()
            ->field('status')->notEqual(Project::DRAFT)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param Provider $provider
     *
     * @return mixed
     */
    public function findByProvider(Provider $provider)
    {
        $id = new \MongoId($provider->getId());
        $expr = ['provider.$id' => new \MongoId($id)];

        return $this
            ->createQueryBuilder()
            ->field('providers')->elemMatch($expr)
            ->getQuery()
            ->execute()
        ;
    }
}
