<?php

namespace Component\Pagination;

/**
 * Interfaz para paginadores genéricos.
 */
interface PaginatorInterface
{
    /**
     * @param mixed $collection
     * @param int   $skip
     * @param int   $limit
     *
     * @return array
     */
    public function paginate($collection, $skip = 0, $limit = 0);
}
