<?php

namespace Persistence\Repository;

use Persistence\Model\Project;
use Persistence\Model\Provider;
use Persistence\Model\User;

/**
 * Repositorio para mensajes.
 */
class MessageRepository extends AbstractDocumentRepository
{
    /**
     * @param User $user
     *
     * @return mixed
     */
    public function findUnreadBy(User $user)
    {
        $qb = $this->createQueryBuilder()
            ->sort('created', 'desc')
            ->field('readBy')->notEqual($user->getId())
            ->field('author.$id')->notEqual(new \MongoId($user->getId()))
        ;

        if ($user->hasRole('ROLE_PROVIDER')) {
            $qb->field('provider')->references($user->getProvider());
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param Project $project
     * @param User    $user
     *
     * @return mixed
     */
    public function countUnreadByProject(Project $project, User $user)
    {
        return $this->createQueryBuilder()
            ->sort('created', 'desc')
            ->field('readBy')->notEqual($user->getId())
            ->field('author.$id')->notEqual(new \MongoId($user->getId()))
            ->field('project')->references($project)
            ->getQuery()
            ->count()
        ;
    }

    /**
     * @param User  $user
     * @param array $messages
     */
    public function markAsReadBy(User $user, $messages)
    {
        $ids = [];

        foreach ($messages as $message) {
            $ids[] = $message->getId();
        }

        $this
            ->createQueryBuilder()
            ->update()
            ->multiple(true)
            ->field('readBy')->push($user->getId())
            ->field('id')->in($ids)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param Project  $project
     * @param Provider $provider
     *
     * @return mixed
     */
    public function buildByProjectAndProvider(
        Project $project,
        Provider $provider
    ) {
        return $this
            ->createQueryBuilder()
            ->field('project')->references($project)
            ->field('provider')->references($provider)
            ->sort('created', 'asc')
        ;
    }

    /**
     * @param Project  $project
     * @param Provider $provider
     *
     * @return mixed
     */
    public function findByProjectAndProvider(
        Project $project,
        Provider $provider
    ) {
        return $this
            ->buildByProjectAndProvider($project, $provider)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @param Project  $project
     * @param Provider $provider
     *
     * @return mixed
     */
    public function findLastByProjectAndProvider(
        Project $project,
        Provider $provider
    ) {
        return $this
            ->buildByProjectAndProvider($project, $provider)
            ->sort('created', 'desc')
            ->limit(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @param Provider $provider
     *
     * @return mixed
     */
    public function findLastByProvider(Provider $provider)
    {
        return $this
            ->createQueryBuilder()
            ->field('provider')->references($provider)
            ->sort('created', 'desc')
            ->limit(1)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function distinctProvidersByProject(Project $project)
    {
        $doctype = 'Persistence\Model\Provider';
        $references = $this
            ->createQueryBuilder()
            ->distinct('provider')
            ->field('project')->references($project)
            ->getQuery()
            ->execute()
        ;
        $providers = [];

        foreach ($references as $reference) {
            $providers[] = $this->dm->find($doctype, $reference['$id']);
        }

        return $providers;
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function findConversationsByProject(Project $project)
    {
        $conversations = [];
        $providers = $this->distinctProvidersByProject($project);

        foreach ($providers as $provider) {
            $messages = $this->findByProjectAndProvider($project, $provider);
            $last = $this->findLastByProjectAndProvider($project, $provider);

            $conversations[] = [
                'provider' => $provider,
                'count' => $messages->count(),
                'last' => $last,
            ];
        }

        return $conversations;
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
