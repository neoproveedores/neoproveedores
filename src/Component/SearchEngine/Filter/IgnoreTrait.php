<?php

namespace Component\SearchEngine\Filter;

use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;

/**
 * Ignorar resultados por su id.
 */
trait IgnoreTrait
{
    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     *
     * @return self
     */
    protected function ignoreById(ParameterBag $query, Builder $builder)
    {
        $ids = [];

        if ($ignore = $query->get('ignore')) {
            foreach ($ignore as $id) {
                if (! empty($id)) {
                    $ids[] = new \MongoId($id);
                }
            }
        }

        if (! empty($ids)) {
            $builder->field('_id')->notIn($ids);
        }

        return $this;
    }
}
