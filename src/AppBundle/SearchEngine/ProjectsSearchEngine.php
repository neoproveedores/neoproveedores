<?php

namespace AppBundle\SearchEngine;

use Component\SearchEngine\SearchEngineInterface;
use Component\SearchEngine\Filter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;
use Persistence\Repository\ProjectRepository;
use Persistence\Model\Project;
use Persistence\Model\ProjectProvider;

/**
 * Motor de búsqueda de proveedores.
 */
class ProjectsSearchEngine implements SearchEngineInterface
{
    use Filters\AbilitiesTrait;
    use Filter\StatusTrait;
    use Filter\WordsTrait;
    use Filter\SortTrait;
    use Filter\KeywordsTrait;

    const ABILITIES_FIELD = 'abilities.$id';

    /**
     * @var Persistence\Repository\ProjectRepository
     */
    protected $repository;

    /**
     * @var Persistence\Model\User
     */
    protected $projectManager;

    /**
     * @var Persistence\Model\Provider
     */
    protected $provider;

    /**
     * @param ProjectRepository $repository
     */
    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param null|User $projectManager
     *
     * @return self
     */
    public function setProjectManager($projectManager)
    {
        $this->projectManager = $projectManager;

        return $this;
    }

    /**
     * @param null|Provider $provider
     *
     * @return self
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function search(ParameterBag $query)
    {
        $builder = $this->createQueryBuilder();

        $this
            ->searchByKeywords($query, $builder, false)
            ->searchByStatus($query, $builder)
            ->searchByAbilities($query, $builder)
            ->searchByProvider($query, $builder)
            ->sortBy($query, $builder)
        ;

        return $builder->getQuery()->execute();
    }

    /**
     * @param  string $query
     * @return Cursor
     */
    public function quickSearch($query)
    {
        $regex = new \MongoRegex("/$query/i");

        return $this->createQueryBuilder()
            ->field('name')->equals($regex)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder()
    {
        $builder = $this->repository->createQueryBuilder();

        if (! $this->projectManager) {
            $builder->field('status')->notEqual(Project::DRAFT);
        }

        if ($this->projectManager) {
            $builder->field('author')->references($this->projectManager);
        } else if ($this->provider) {
            $id = $this->provider->getId();
            $expr = [
                'status' => ['$not' => ['$eq' => ProjectProvider::REJECTED]],
                'provider.$id' => new \MongoId($id),
            ];

            $builder->field('providers')->elemMatch($expr);
        }

        return $builder;
    }

    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @return self
     */
    protected function searchByProvider(ParameterBag $query, Builder $builder)
    {
        if ($query->has('provider')) {
            $id = new \MongoId($query->get('provider'));
            $expr = ['provider.$id' => new \MongoId($id)];

            $builder->field('providers')->elemMatch($expr);
        }

        return $this;
    }
}
