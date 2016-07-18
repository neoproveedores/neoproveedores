<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Persistence\Fixtures\AbilityFixtures;
use Persistence\Model\Ability;

/**
 * Añade habilidades
 */
class AbilitiesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('neo:abilities:add')
            ->setDescription('Añade habilidades')
            ->addArgument('abilities', InputArgument::IS_ARRAY)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dm = $container->get('doctrine.odm.mongodb.document_manager');
        $repository = $container->get('persistence.ability_repository');
        $names = $input->getArgument('abilities') ? : AbilityFixtures::names();

        foreach ($names as $name) {
            if ($ability = $repository->findOneByName($name)) {
                $output->writeln('Habilidad ya existente: '.$name);
            } else {
                $ability = new Ability($name);
                $dm->persist($ability);
                $dm->flush();
                $output->writeln('Añadida habilidad: '.$name);
            }
        }
    }
}
