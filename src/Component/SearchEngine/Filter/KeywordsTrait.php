<?php

namespace Component\SearchEngine\Filter;

use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;
use Component\SearchEngine\Indexer\KeywordExtractor;

/**
 * Filtra resultados por palabras en distintos campos.
 *
 * @version 1.1
 */
trait KeywordsTrait
{
    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @param array        $fields
     *
     * @return self
     */
    protected function searchByKeywords(
        ParameterBag $query,
        Builder $builder,
        $exact = true
    ) {
        $keywords = KeywordExtractor::extract($query->get('keywords'));

        if (! empty($keywords)) {
            $keywords = $exact ? $keywords : array_map(function ($keyword) {
                return new \MongoRegex('/'.$keyword.'/');
            }, $keywords);

            $builder->addOr($builder->expr()->field('keywords')->in($keywords));
        }

        return $this;
    }
}
