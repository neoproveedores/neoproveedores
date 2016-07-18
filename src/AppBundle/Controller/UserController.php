<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Persistence\Model\Provider;
use Persistence\Model\Project;
use Component\Pagination\DocumentSortHelper;

/**
 * Controlador de usuarios para agencias
 */
class UserController extends Controller
{
    /**
     * Listado de proveedores
     *
     * @Route("/users", name="users")
     * @Security("has_role('ROLE_VIEW_USERS')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $template = 'user/index.html.twig';
        $paginator = $this->get('app.document_paginator');
        $users = $this->get('app.search.users')->search($request->query);
        $pagination = $paginator->paginate($users, $request->get('skip'));

        if ($request->isXmlHttpRequest()) {
            $template = 'user/users.html.twig';
        }

        return $this->render($template, [
            'area' => 'users',
            'pagination' => $pagination,
            'next_url' => $paginator->generateNextUrl($request, $pagination),
            'sorter' => new DocumentSortHelper($this->get('router'), $request),
        ]);
    }
}
