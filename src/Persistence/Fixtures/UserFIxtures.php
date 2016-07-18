<?php

namespace Persistence\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Persistence\Model\User;
use Persistence\Model\Contact;

/**
 * Usuarios de ejemplo.
 */
class UserFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $dm)
    {
        $this->createManager($dm);
        $this->createProjectManager($dm);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

    protected function createManager($dm)
    {
        $user = new User();
        $contact = new Contact('Neolabels', null, null);

        $user
            ->setEmail('hola@neolabels.com')
            ->setPlainPassword('interacso')
            ->setEnabled(true)
            ->addRole('ROLE_MANAGER')
            ->setContact($contact)
        ;

        $dm->persist($user);
        $dm->flush();

        $this->addReference('manager', $user);
    }

    protected function createProjectManager($dm)
    {
        $user = new User();
        $contact = new Contact('Project', 'Manager', null);

        $user
            ->setEmail('project.manager@interacso.com')
            ->setPlainPassword('interacso')
            ->setEnabled(true)
            ->addRole('ROLE_PROJECT_MANAGER')
            ->setContact($contact)
        ;

        $dm->persist($user);
        $dm->flush();

        $this->addReference('project_manager', $user);
    }
}
