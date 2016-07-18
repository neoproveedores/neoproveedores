<?php

namespace AppBundle\SearchEngine;

use Component\SearchEngine\SearchEngineInterface;
use Component\SearchEngine\Filter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;
use Persistence\Repository\UserRepository;

/**
 * Motor de búsqueda de usuarios
 */
class UsersSearchEngine implements SearchEngineInterface
{
    use Filter\WordsTrait;
    use Filter\SortTrait;

    /**
     * @var Persistence\Repository\UserRepository
     */
    protected $repository;

    /**
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function search(ParameterBag $query)
    {
        $builder = $this->repository->createQueryBuilder();
        $wordsFields = [
            'name',
            'contact.businessName',
            'contact.firstName',
            'contact.lastName',
        ];

        $this
            ->searchByWords($query, $builder, $wordsFields)
            ->searchByRoles($query, $builder)
            ->sortBy($query, $builder)
        ;

        return $builder->getQuery()->execute();
    }

    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @return self
     */
    protected function searchByRoles(ParameterBag $query, Builder $builder)
    {
        if ($query->has('roles')) {
            $rol = $query->get('roles');

            $builder->field('roles')->equals(new \MongoRegex("/$rol/i"));
        }

        return $this;
    }
}
