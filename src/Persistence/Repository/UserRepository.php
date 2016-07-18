<?php

namespace Persistence\Repository;

/**
 * Repositorio para usuarios.
 */
class UserRepository extends AbstractDocumentRepository
{
    /**
     * @param  string $role
     * @return null|User
     */
    public function findByRole($role)
    {
        return $this
            ->createQueryBuilder()
            ->field('roles')->equals($role)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param  string $role
     * @return null|User
     */
    public function findOneByRole($role)
    {
        return $this
            ->createQueryBuilder()
            ->field('roles')->equals($role)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @param  string $role
     * @param  string $notification
     * @return null|User
     */
    public function findByRoleWithNotification($role, $notification)
    {
        return $this
            ->createQueryBuilder()
            ->field('roles')->equals($role)
            ->field('notifications.enabled')->equals(true)
            ->field('notifications.enabledEvents')->equals($notification)
            ->getQuery()
            ->execute()
        ;
    }
}
