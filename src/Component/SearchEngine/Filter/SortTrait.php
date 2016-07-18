<?php

namespace Component\SearchEngine\Filter;

use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;

/**
 * Ordena los resultados
 */
trait SortTrait
{
    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @return self
     */
    protected function sortBy(ParameterBag $query, Builder $builder)
    {
        if ($query->has('sort')) {
            $field = $query->get('sort');
            $direction = $query->get('direction', 'asc');
        } else {
            $field = 'created';
            $direction = 'desc';
        }

        $builder->sort($field, $direction);
    }
}
