<?php

namespace Persistence\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Persistence\Model\Project;
use Persistence\Model\Timing;
use Persistence\Model\File;
use Persistence\Model\ProjectProvider;
use Persistence\Model\Budget;
use Persistence\Model\Amount;

/**
 * Proyectos de ejemplo.
 */
class ProjectsFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return array
     */
    public static function names()
    {
        return [
            'Diseño de interacción para una app',
            'Diseño gráfico para blog',
            'Wireframes intranet',
            'Diseño de interacción para una web',
            'Seo para un portal',
            'Copys para una web',
            'Desarrollo de app nativa para iOS',
            'Rediseño de imagen corporativa',
            'Campaña para televisión',
            'Evento promocional',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $dm)
    {
        foreach (self::names() as $index => $name) {
            $project = new Project();
            $end = sprintf('+%s days', rand(3, 10));
            $timing = new Timing(new \DateTime(), new \DateTime($end));
            $briefing = file_get_contents(__DIR__.'/Resources/briefing.html');
            $description = file_get_contents(__DIR__.'/Resources/lorem.txt');

            $project
                ->setCode('CRTE'.str_pad($index, 4, '0', STR_PAD_LEFT).'-X')
                ->setName($name)
                ->setDescription($description)
                ->setAbilities($this->chooseRandomAbilities())
                ->setStatus($this->chooseRandomStatus())
                ->setTiming($timing)
                ->setBriefing($briefing)
                ->setFiles($this->createRandomFiles())
                ->setProviders($this->createRandomProviders())
            ;

            if ($project->hasStatus([Project::ASSIGNED, Project::CLOSED])) {
                $providers = $project->getProviders();
                $provider = $providers[array_rand($providers)];
                $provider->setAssignation(new \DateTime());
                $provider->setStatus(ProjectProvider::ASSIGNED);
            }

            $this->addReference('project-'.$index, $project);
            $dm->persist($project);
            ++$index;
        }

        $status = ProjectProvider::INVITED;
        $project = $this->getReference('project-0');
        $provider = $this->getReference('provider-0');
        $projectProvider = $this->createProvider($provider, $status);

        $project->setStatus(Project::SENT);
        $project->addProjectProvider($projectProvider);
        $dm->persist($project);

        $project = new Project();
        $end = sprintf('+%s days', rand(3, 10));
        $timing = new Timing(new \DateTime(), new \DateTime($end));
        $briefing = file_get_contents(__DIR__.'/Resources/briefing.html');
        $description = file_get_contents(__DIR__.'/Resources/lorem.txt');
        $project
            ->setAuthor($this->getReference('project_manager'))
            ->setCode('CRTE'.str_pad($index, 4, '0', STR_PAD_LEFT).'-X')
            ->setName('Desarrollo de aplicación web')
            ->setDescription($description)
            ->setAbilities($this->chooseRandomAbilities())
            ->setStatus(Project::DRAFT)
            ->setTiming($timing)
            ->setBriefing($briefing)
            ->setFiles($this->createRandomFiles())
        ;
        $dm->persist($project);

        $dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * @param int $number
     *
     * @return array
     */
    protected function chooseRandomAbilities($number = 2)
    {
        $abilities = [];

        foreach (array_rand(AbilityFixtures::names(), $number) as $index) {
            $name = AbilityFixtures::names()[$index];
            $ability = $this->getReference('ability-'.$name);
            $abilities[] = $ability;
        }

        return $abilities;
    }

    /**
     * @return string
     */
    protected function chooseRandomStatus()
    {
        $options = array_slice(Project::getStatusOptions(), 1);

        return $options[array_rand($options)];
    }

    /**
     * @param int $number
     *
     * @return array
     */
    protected function createRandomFiles($number = 3)
    {
        $files = [];
        $names = [
            'Presentación preliminar.keynote',
            'Diseño final.sketch',
            'Vídeo de introducción.mp4',
            'Recursos gráficos.zip',
            'Borrador del copy.paper',
            'Fotos de stock.zip',
        ];

        foreach (array_rand($names, $number) as $index) {
            $file = new File();
            $file->setName($names[$index])->setSize(rand(128, 4096));
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @param int $number
     *
     * @return array
     */
    protected function createRandomProviders($number = 2)
    {
        $providers = [];

        for ($index = 0; $index < $number; ++$index) {
            $status = ProjectProvider::BUDGETED;
            $provider = $this->getReference('provider-'.rand(1, 9));
            $projectProvider = $this->createProvider($provider, $status);

            $providers[] = $projectProvider;
        }

        return $providers;
    }

    protected function createProvider($provider, $status)
    {
        $projectProvider = new ProjectProvider();

        if ($status == ProjectProvider::BUDGETED) {
            $budget = new Budget();
            $timing = new Timing(new \DateTime(), new \DateTime('+3 days'));

            $budget
                ->setAmount(new Amount(rand(5000, 50000)))
                ->setNotes(file_get_contents(__DIR__.'/Resources/lorem.txt'))
                ->setTiming($timing)
                ->setFiles($this->createRandomFiles(2))
            ;
            $projectProvider->setBudget($budget);
        }

        $projectProvider->setStatus($status)->setProvider($provider);

        return $projectProvider;
    }
}
