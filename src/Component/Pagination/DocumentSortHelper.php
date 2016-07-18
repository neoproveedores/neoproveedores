<?php

namespace Component\Pagination;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ayuda a crear urls para ordenar un listado de documentos.
 */
class DocumentSortHelper
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @param Router  $router
     * @param Request $request
     */
    public function __construct(Router $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @param string $field
     * @return string
     */
    public function direction($field)
    {
        if ($field == $this->request->get('sort')) {
            return $this->request->get('direction', 'asc');
        }
    }

    /**
     * @param string      $field
     * @param null|string $direction
     * @return string
     */
    public function generate($field, $direction = null)
    {
        $route = $this->request->attributes->get('_route');
        $params = $this->request->attributes->get('_route_params');
        $query = $this->request->query->all();

        if ($field == $this->request->get('sort')) {
            if ($dir = $this->request->get('direction')) {
                $direction = $dir == 'asc' ? 'desc' : 'asc';
            }
        }

        $query['sort'] = $field;
        $query['direction'] = $direction ? $direction : 'asc';

        return $this->router->generate($route, array_merge($params, $query));
    }
}
