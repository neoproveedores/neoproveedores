<?php

namespace Persistence\Repository;

use Persistence\Model\Provider;

/**
 * Repositorio para proveedores.
 */
class ProviderRepository extends AbstractDocumentRepository
{
    /**
     * @return mixed
     */
    public function findPublic()
    {
        return $this
            ->createQueryBuilder()
            ->field('status')->equals(Provider::ACTIVE)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @return mixed
     */
    public function findPending()
    {
        return $this
            ->createQueryBuilder()
            ->field('status')->equals(Provider::DRAFT)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @return mixed
     */
    public function findFirst()
    {
        return $this
            ->createQueryBuilder()
            ->getQuery()
            ->execute()
            ->getSingleResult()
        ;
    }
}
