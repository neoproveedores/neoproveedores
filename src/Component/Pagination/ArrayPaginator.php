<?php

namespace Component\Pagination;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pagina un array.
 */
class ArrayPaginator implements PaginatorInterface
{
    /**
     * @var int
     */
    const LIMIT = 10;

    /**
     * @var Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * {@inheritdoc}
     */
    public function paginate($array, $skip = 0, $limit = 0)
    {
        $limit = $limit ? $limit : self::LIMIT;
        $total = count($array);
        $items = array_slice($array, $skip, $limit);
        $more = $total - $skip - $limit;

        if ($more < 0) {
            $more = 0;
        }

        return [
            'skip' => $skip,
            'limit' => $limit,
            'total' => $total,
            'items' => $items,
            'more' => $more > $limit ? $limit : $more,
            'next' => $more ? $skip + $limit : null,
        ];
    }

    /**
     * @param Request $request
     * @param array   $pagination
     *
     * @return string
     */
    public function generateNextUrl(Request $request, array $pagination)
    {
        $route = $request->attributes->get('_route');
        $params = $request->attributes->get('_route_params');
        $query = $request->query->all();
        $query['skip'] = $pagination['next'];

        return $this->router->generate($route, array_merge($params, $query));
    }
}
