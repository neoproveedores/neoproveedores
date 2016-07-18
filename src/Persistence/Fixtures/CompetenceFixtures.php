<?php

namespace Persistence\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Persistence\Model\Competence;

/**
 * Modelos de ejemplo.
 */
class CompetenceFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $dm)
    {
        foreach (self::names() as $name) {
            $competence = new Competence($name);
            $dm->persist($competence);
            $this->addReference('competence-'.$name, $competence);
        }

        $dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @return array
     */
    public static function names()
    {
        return [
            'Calidad en el trabajo',
            'Trabajo en equipo',
            'Puntualidad',
            'Creatividad e innovación',
            'Relación con el equipo',
            'Servicio al cliente',
            'Gestión de cambios',
            'Organización y control',
        ];
    }
}
