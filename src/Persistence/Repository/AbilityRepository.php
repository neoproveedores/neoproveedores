<?php

namespace Persistence\Repository;

/**
 * Repositorio para habilidades.
 */
class AbilityRepository extends AbstractDocumentRepository
{
    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this
            ->createQueryBuilder()
            ->sort('metrics.providersUsed', 'desc')
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param  string $name
     * @return null|Ability
     */
    public function findOneByName($name)
    {
        return $this
            ->createQueryBuilder()
            ->field('name')->equals(new \MongoRegex("/$name/i"))
            ->getQuery()
            ->execute()
            ->getSingleResult()
        ;
    }
}
