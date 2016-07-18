<?php

namespace AppBundle\Form\Loader;

use Symfony\Bridge\Doctrine\Form\ChoiceList\DoctrineChoiceLoader;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Persistence\Model\Ability;
use Persistence\Repository\AbilityRepository;

/**
 * Crea nuevas habilidades si no existen
 */
class AbilitiesLoader implements ChoiceLoaderInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct($dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        $abilities = $this->getRepository()->findAll();

        foreach ($abilities as $ability) {
            $choices[$ability->getId()] = $ability->getName();
        }

        return new ArrayChoiceList($abilities, function ($ability) {
            return $ability->getId();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        $choices = [];
        $repository = $this->getRepository();

        foreach ($values as $value) {
            $ability = $repository->find($value);

            if (! $ability) {
                $ability = new Ability($value);
            }

            $choices[] = $ability;
        }

        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        $values = [];

        foreach ($choices as $index => $item) {
            if ($item instanceof Ability) {
                $values[$index] = $item->getId();
            }
        }

        return $values;
    }

    /**
     * @return AbilityRepository
     */
    protected function getRepository()
    {
        return $this->dm->getRepository('Persistence\Model\Ability');
    }
}
