<?php

namespace Persistence\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Persistence\Model\Event;
use Persistence\Model\ProjectProvider;

/**
 * Eventos de ejemplo.
 */
class EventFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $dm)
    {
        foreach (ProjectsFixtures::names() as $index => $name) {
            $project = $this->getReference('project-'.$index);

            foreach ($project->getProviders() as $pp) {
                $provider = $pp->getProvider();
                $user = $provider->getUser();
                $event = new Event(null, $user, $project, $provider);

                if ($pp->hasStatus(ProjectProvider::BUDGETED)) {
                    $event->setKind(Event::ACCEPT);

                    $be = new Event(Event::BUDGET, $user, $project, $provider);
                    $dm->persist($be);
                } else if ($pp->hasStatus(ProjectProvider::REJECTED)) {
                    $event->setKind(Event::REJECT);
                } else {
                    continue;
                }

                $dm->persist($event);
            }
        }

        $dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }
}
