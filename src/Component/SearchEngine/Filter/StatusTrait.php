<?php

namespace Component\SearchEngine\Filter;

use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;

/**
 * Filtra resultados por estado.
 */
trait StatusTrait
{
    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     *
     * @return self
     */
    protected function searchByStatus(ParameterBag $query, Builder $builder)
    {
        if ($status = $query->get('status')) {
            $builder->field('status')->equals($status);
        }

        return $this;
    }
}
