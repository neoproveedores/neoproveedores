<?php

namespace Component\SearchEngine\Filter;

use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;
use Component\SearchEngine\SearchEngineInterface;

/**
 * Filtra resultados por palabras en distintos campos.
 */
trait WordsTrait
{
    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @param array        $fields
     *
     * @return self
     */
    protected function searchByWords(
        ParameterBag $query,
        Builder $builder,
        array $fields
    ) {
        if ($words = $query->get('words')) {
            $words = array_filter(explode(' ', $words), function ($word) {
                return strlen($word) > SearchEngineInterface::WORD_MIN_LENGTH;
            });
            $exp = implode('|', $words);
            $re = new \MongoRegex("/$exp/i");

            foreach ($fields as $field) {
                $builder->addOr($builder->expr()->field($field)->equals($re));
            }
        }

        return $this;
    }
}
