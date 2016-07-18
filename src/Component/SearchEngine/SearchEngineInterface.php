<?php

namespace Component\SearchEngine;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Interfaz para motores de búsqueda.
 */
interface SearchEngineInterface
{
    const WORD_MIN_LENGTH = 2;

    /**
     * Busca resultados a partir de los parámetros de una consulta
     *
     * @param ParameterBag $query
     *
     * @return mixed
     */
    public function search(ParameterBag $query);
}
