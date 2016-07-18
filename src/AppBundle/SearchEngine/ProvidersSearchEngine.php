<?php

namespace AppBundle\SearchEngine;

use Component\SearchEngine\SearchEngineInterface;
use Component\SearchEngine\Filter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;
use Persistence\Repository\ProviderRepository;
use Persistence\Model\Provider;

/**
 * Motor de búsqueda de proveedores
 */
class ProvidersSearchEngine implements SearchEngineInterface
{
    use Filters\AbilitiesTrait;
    use Filter\StatusTrait;
    use Filter\WordsTrait;
    use Filter\SortTrait;
    use Filter\KeywordsTrait;
    use Filter\IgnoreTrait;

    const ABILITIES_FIELD = 'skills.ability.$id';

    /**
     * @var Persistence\Repository\ProviderRepository
     */
    protected $repository;

    /**
     * @param ProviderRepository $repository
     */
    public function __construct(ProviderRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public static function wordFields()
    {
        return [
            'contact.businessName',
            'contact.firstName',
            'contact.lastName',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function search(ParameterBag $query)
    {
        $builder = $this->createQueryBuilder();

        $this
            ->searchByKeywords($query, $builder, false)
            ->searchByAbilities($query, $builder)
            ->searchByHourRate($query, $builder)
            ->searchByRatings($query, $builder)
            ->searchByStatus($query, $builder)
            ->ignoreById($query, $builder)
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
        $builder = $this->createQueryBuilder();

        foreach (self::wordFields() as $field) {
            $builder->addOr($builder->expr()->field($field)->equals($regex));
        }

        return $builder->getQuery()->execute();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder()
    {
        $builder = $this->repository->createQueryBuilder();

        $builder->field('status')->equals(Provider::ACTIVE);

        return $builder;
    }

    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @return self
     */
    protected function searchByHourRate(ParameterBag $query, Builder $builder)
    {
        if ($query->get('rate_min') and $query->get('rate_max')) {
            $min = floatval($query->get('rate_min'));
            $max = floatval($query->get('rate_max'));

            $builder->field('hourRate.value')->range($min, $max);
        }

        return $this;
    }

    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @return self
     */
    protected function searchByRatings(ParameterBag $query, Builder $builder)
    {
        if ($query->has('ratings')) {
            $rating = [];
            foreach ($query->get('ratings') as $rating) {
                $ratings[] = intval($rating);
            }

            $builder->field('metrics.averageRating')->in($ratings);
        }

        return $this;
    }
}
