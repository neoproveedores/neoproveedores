<?php

namespace AppBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use Symfony\Component\HttpFoundation\ParameterBag;
use AppBundle\SearchEngine\ProvidersSearchEngine;
use AppBundle\SearchEngine\Filters;
use Persistence\Model\Provider;
use Persistence\Model\Metrics;

/**
 * Mantiene actualizadas las métricas de las habilidades
 */
class AbilityMetricsListener
{
    use Filters\AbilitiesTrait;

    const ABILITIES_FIELD = 'skills.ability.$id';

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        if ($event->getDocument() instanceof Provider) {
            $this->updateAbilityMetrics($event);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getDocument() instanceof Provider) {
            $this->updateAbilityMetrics($event);
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    protected function updateAbilityMetrics(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();
        $dm = $event->getDocumentManager();
        $repository = $dm->getRepository('Persistence\Model\Provider');

        foreach ($document->getSkills() as $skill) {
            if ($ability = $skill->getAbility()) {
                $metrics = $ability->getMetrics() ? : new Metrics();
                $builder = $repository->createQueryBuilder();
                $query = new ParameterBag(['abilities' => [$ability->getId()]]);

                $this->searchByAbilities($query, $builder);
                $count = $builder->getQuery()->execute()->count();
                $metrics->setProvidersUsed($count);
                $ability->setMetrics($metrics);
                $dm->persist($ability);
                $dm->flush($ability);
            }
        }
    }
}
