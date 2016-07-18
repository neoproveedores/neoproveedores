<?php

namespace AppBundle\SearchEngine\Filters;

use Symfony\Component\HttpFoundation\ParameterBag;
use Doctrine\MongoDB\Query\Builder;

/**
 * Filtra resultados por abilidades.
 */
trait AbilitiesTrait
{
    /**
     * @param ParameterBag $query
     * @param Builder      $builder
     * @return self
     */
    protected function searchByAbilities(ParameterBag $query, Builder $builder)
    {
        if ($query->has('abilities')) {
            $ids = [];
            foreach ($query->get('abilities') as $ability) {
                if (! empty($ability)) {
                    $ids[] = new \MongoId($ability);
                }
            }

            $builder->field(self::ABILITIES_FIELD)->in($ids);
        }

        return $this;
    }
}
