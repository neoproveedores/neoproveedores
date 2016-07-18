<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Indexa documentos
 */
class IndexerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('neo:indexer:index')
            ->setDescription('Indexa todos los documentos')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $providers = $this->getContainer()->get('app.search.indexer.providers');
        $projects = $this->getContainer()->get('app.search.indexer.projects');

        $output->write('Indexing providers... ');
        $status = $providers->indexAll();
        $output->writeln($status.' providers indexed');

        $output->write('Indexing projects... ');
        $status = $projects->indexAll();
        $output->writeln($status.' projects indexed');
    }
}
