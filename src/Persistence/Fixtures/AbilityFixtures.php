<?php

namespace Persistence\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Persistence\Model\Ability;

/**
 * Habilidades de ejemplo.
 */
class AbilityFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $dm)
    {
        foreach (self::names() as $name) {
            $ability = new Ability($name);
            $dm->persist($ability);
            $this->addReference('ability-'.$name, $ability);
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
            'HTML',
            'CSS',
            'JS',
            'UX',
            'UI',
            'SEO',
            'SEM',
            'IOS',
            'ANDROID',
            'DISEÑO',
            'COPY',
            'ILUSTRACIÓN',
            'FLASH',
            'WORDPRESS',
            'MAGENTO',
            'PHOTOSHOP',
            'AS3',
        ];
    }
}
