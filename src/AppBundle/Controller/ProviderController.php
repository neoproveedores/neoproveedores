<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Persistence\Model\Provider;
use Persistence\Model\Project;
use Component\Pagination\DocumentSortHelper;

/**
 * Controlador de sólo lectura para proveedores
 */
class ProviderController extends Controller
{
    use Behaviors\NavigationTrait;

    /**
     * Listado de proveedores
     *
     * @Route("/providers", name="providers")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $request->query;
        $template = 'provider/index.html.twig';
        $paginator = $this->get('app.document_paginator');
        $abilities = $this->get('persistence.ability_repository')->findAll();
        $providers = $this->get('app.search.providers')->search($query);
        $pagination = $paginator->paginate($providers, $request->get('skip'));
        $pending = $this->get('persistence.provider_repository')->findPending();

        if ($request->isXmlHttpRequest()) {
            $template = 'provider/providers.html.twig';
        }

        $request->getSession()->set('query', $query);

        return $this->render($template, [
            'area' => 'providers',
            'abilities' => $abilities,
            'pagination' => $pagination,
            'next_url' => $paginator->generateNextUrl($request, $pagination),
            'sorter' => new DocumentSortHelper($this->get('router'), $request),
            'pending' => $pending,
        ]);
    }

    /**
     * Búsqueda rápida de proveedores
     *
     * @Route("/search/providers", name="search_providers")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $items = [];
        $searchEngine = $this->get('app.search.providers');

        if ($query = $request->get('query')) {
            foreach ($searchEngine->quickSearch($query) as $provider) {
                $items[] = [
                    'id' => $provider->getId(),
                    'name' => (string) $provider,
                    'url' => $this->generateUrl('provider_preview', [
                        'id' => $provider->getId(),
                    ]),
                ];
            }
        }

        return new JsonResponse($items);
    }

    /**
     * Listado de proveedores pendientes.
     *
     * @Route("/pending/providers", name="pending_providers")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function pendingAction(Request $request)
    {
        $template = 'provider/pending.html.twig';
        $items = $this->get('persistence.provider_repository')->findPending();
        $paginator = $this->get('app.document_paginator');
        $pagination = $paginator->paginate($items, $request->get('skip'));

        if ($request->isXmlHttpRequest()) {
            $template = 'provider/providers.html.twig';
        }

        return $this->render($template, [
            'area' => 'providers',
            'pagination' => $pagination,
            'next_url' => $paginator->generateNextUrl($request, $pagination),
            'sorter' => new DocumentSortHelper($this->get('router'), $request),
        ]);
    }

    /**
     * Vista previa de un proveedor
     *
     * @Route("/provider/{id}/preview", name="provider_preview")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function previewAction(Provider $provider)
    {
        $providers = $this->getLastSearchResults();
        $ratings = $this
            ->get('persistence.rating_repository')
            ->findLastByProvider($provider)
            ->toArray()
        ;
        $lastMessage = $this
            ->get('persistence.message_repository')
            ->findLastByProvider($provider)
        ;

        return $this->render('provider/preview.html.twig', [
            'area' => 'providers',
            'section' => 'preview',
            'provider' => $provider,
            'competences' => $provider->getCompetencesRatingsAverage($ratings),
            'rating_info' => $this->getRatingInfo($ratings),
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
            'last_message' => $lastMessage,
        ]);
    }

    /**
     * Contactos de un proveedor
     *
     * @Route("/provider/{id}/contacts", name="provider_contacts")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function contactsAction(Provider $provider)
    {
        $providers = $this->getLastSearchResults();

        return $this->render('provider/contacts.html.twig', [
            'area' => 'providers',
            'section' => 'contacts',
            'provider' => $provider,
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
        ]);
    }

    /**
     * Habilidades de un proveedor
     *
     * @Route("/provider/{id}/skills", name="provider_skills")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function skillsAction(Provider $provider)
    {
        $providers = $this->getLastSearchResults();

        return $this->render('provider/skills.html.twig', [
            'area' => 'providers',
            'section' => 'skills',
            'provider' => $provider,
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
        ]);
    }

    /**
     * Proyectos de un proveedor
     *
     * @Route("/provider/{id}/projects", name="provider_projects")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function projectsAction(Provider $provider)
    {
        $template = 'provider/projects.html.twig';
        $request = $this->getRequest();
        $query = $request->query;
        $query->set('provider', $provider->getId());
        $providers = $this->getLastSearchResults();
        $paginator = $this->get('app.document_paginator');
        $abilities = $this->get('persistence.ability_repository')->findAll();
        $projects = $this->get('app.search.projects')->search($query);
        $pagination = $paginator->paginate($projects, $request->get('skip'));

        if ($request->isXmlHttpRequest()) {
            $template = 'provider/projects_list.html.twig';
        }

        return $this->render($template, [
            'area' => 'providers',
            'section' => 'projects',
            'abilities' => $abilities,
            'status' => Project::getStatusOptions(),
            'provider' => $provider,
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
            'pagination' => $pagination,
            'next_url' => $paginator->generateNextUrl($request, $pagination),
            'sorter' => new DocumentSortHelper($this->get('router'), $request),
        ]);
    }

    /**
     * Valoración de un proveedor
     *
     * @Route("/provider/{id}/rating", name="provider_rating")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function ratingAction(Provider $provider)
    {
        $providers = $this->getLastSearchResults();
        $ratings = $this
            ->get('persistence.rating_repository')
            ->findLastByProvider($provider)
            ->toArray()
        ;

        return $this->render('provider/rating.html.twig', [
            'area' => 'providers',
            'section' => 'rating',
            'provider' => $provider,
            'last_ratings' => $this->getLastRatingsWithNotes($ratings),
            'competences' => $provider->getCompetencesRatings($ratings),
            'rating_info' => $this->getRatingInfo($ratings),
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
        ]);
    }

    /**
     * Valoraciones de un proveedor
     *
     * @Route("/provider/{id}/ratings", name="provider_ratings")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function ratingsAction(Provider $provider)
    {
        $providers = $this->getLastSearchResults();
        $ratings = $this
            ->get('persistence.rating_repository')
            ->findLastByProvider($provider)
            ->toArray()
        ;

        return $this->render('provider/ratings.html.twig', [
            'area' => 'providers',
            'section' => 'rating',
            'provider' => $provider,
            'ratings' => $ratings,
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
        ]);
    }

    /**
     * @return Cursor
     */
    protected function getLastSearchResults()
    {
        $query = $this->getRequest()->getSession()->get('query');

        if (! $query) {
            $query = new ParameterBag();
        }

        return $this->get('app.search.providers')->search($query);
    }

    /**
     * @param  array $ratings
     * @return array
     */
    protected function getLastRatingsWithNotes($ratings)
    {
        $lastRatings = [];

        foreach ($ratings as $rating) {
            if ($rating->getNotes()) {
                $lastRatings[] = $rating;
            }
            if (count($lastRatings) >= 4) {
                break;
            }
        }

        return $lastRatings;
    }

    /**
     * @param  array $ratings
     * @return array
     */
    protected function getRatingInfo($ratings)
    {
        $info = [];
        $ratings = array_reverse($ratings);

        foreach ($ratings as $rating) {
            $info[] = [
                'project' => (string) $rating->getProject(),
                'notes' => $rating->getNotes(),
            ];
        }

        return $info;
    }
}
