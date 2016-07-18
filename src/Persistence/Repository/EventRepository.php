<?php

namespace Persistence\Repository;

use Persistence\Model\Provider;
use Persistence\Model\Project;
use Persistence\Model\Event;
use Persistence\Model\User;

/**
 * Repositorio para eventos.
 */
class EventRepository extends AbstractDocumentRepository
{
    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this
            ->createQueryBuilder()
            ->sort('created', 'desc')
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param string $kind
     * @return mixed
     */
    public function findByKind($kind)
    {
        return $this
            ->createQueryBuilder()
            ->field('kind')->equals($kind)
            ->sort('created', 'desc')
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param DateTime $date
     * @return mixed
     */
    public function findAfter($date)
    {
        return $this
            ->createQueryBuilder()
            ->field('created')->gt($date)
            ->sort('created', 'desc')
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function findLastWithoutUser(User $user)
    {
        return $this
            ->createQueryBuilder()
            ->field('created')->gt($user->getLastTimelineVisit())
            ->field('user.$id')->notEqual(new \MongoId($user->getId()))
            ->field('kind')->notEqual(Event::MESSAGE)
            ->sort('created', 'desc')
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
