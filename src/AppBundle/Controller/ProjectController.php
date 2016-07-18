<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Persistence\Model\Project;
use Persistence\Model\Provider;
use Component\Pagination\DocumentSortHelper;

/**
 * Controlador para proyectos.
 */
class ProjectController extends Controller
{
    use Behaviors\NavigationTrait;

    /**
     * Listado de proyectos.
     *
     * @Route("/projects", name="projects")
     * @Security("has_role('ROLE_VIEW_PROJECTS')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $provider = null;
        $searchEngine = $this->get('app.search.projects');
        $template = 'project/index.html.twig';
        $paginator = $this->get('app.document_paginator');

        if ($request->isXmlHttpRequest()) {
            $template = 'project/projects.html.twig';
        }

        if ($this->isGranted('ROLE_PROJECT_MANAGER')) {
            $searchEngine->setProjectManager($this->getUser());
        } else if ($this->isGranted('ROLE_PROVIDER')) {
            $searchEngine->setProvider($this->getUser()->getProvider());
        }

        $abilities = $this->get('persistence.ability_repository')->findAll();
        $projects = $searchEngine->search($request->query);
        $pagination = $paginator->paginate($projects, $request->get('skip'));

        return $this->render($template, [
            'area' => 'projects',
            'abilities' => $abilities,
            'status' => Project::getStatusOptions(),
            'pagination' => $pagination,
            'next_url' => $paginator->generateNextUrl($request, $pagination),
            'sorter' => new DocumentSortHelper($this->get('router'), $request),
        ]);
    }

    /**
     * Búsqueda rápida de proyectos
     *
     * @Route("/search/projects", name="search_projects")
     * @Security("has_role('ROLE_VIEW_PROJECTS')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $items = [];
        $searchEngine = $this->get('app.search.projects');

        if ($this->isGranted('ROLE_PROVIDER')) {
            $searchEngine->setProvider($this->getUser()->getProvider());
        }

        if ($query = $request->get('query')) {
            foreach ($searchEngine->quickSearch($query) as $project) {
                $items[] = [
                    'id' => $project->getId(),
                    'name' => (string) $project,
                    'url' => $this->generateUrl('project_briefing', [
                        'id' => $project->getId(),
                    ]),
                ];
            }
        }

        return new JsonResponse($items);
    }

    /**
     * Briefing de un proyecto.
     *
     * @Route("/project/{id}/briefing", name="project_briefing")
     * @Security("has_role('ROLE_VIEW_PROJECTS')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function briefingAction(Project $project)
    {
        $provider = null;
        $projectProvider = null;
        $downloadUrl = $this->generateUrl('download_project', [
            'id' => $project->getId(),
            'index' => 'index',
        ]);

        if ($this->isGranted('ROLE_PROVIDER')) {
            $provider = $this->getUser()->getProvider();
            $projectProvider = $project->getProvider($provider);

            if (! $project->hasProvider($provider)) {
                throw $this->createNotFoundException();
            }
        }

        return $this->render('project/briefing.html.twig', [
            'area' => 'projects',
            'section' => 'briefing',
            'project' => $project,
            'provider' => $provider,
            'project_provider' => $projectProvider,
            'download_url' => $downloadUrl,
        ]);
    }

    /**
     * Proveedores de un proyecto.
     *
     * @Route("/project/{id}/providers", name="project_providers")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function providersAction(Project $project)
    {
        $template = 'project/providers.html.twig';
        $request = $this->getRequest();
        $query = $request->query;
        $query->set('status', Provider::ACTIVE);
        $query->set('ignore', $project->getProvidersIds());
        $skip = $request->get('skip');
        $paginator = $this->get('app.array_paginator');
        $providers = $this->get('app.search.providers')->search($query);
        $providers = $this->sortByProject($providers, $project);
        $pagination = $paginator->paginate($providers, $skip, 5);

        if ($request->isXmlHttpRequest()) {
            $template = 'project/providers_list.html.twig';
        }

        return $this->render($template, [
            'area' => 'projects',
            'section' => 'providers',
            'project' => $project,
            'pagination' => $pagination,
            'next_url' => $paginator->generateNextUrl($request, $pagination),
            'sorter' => new DocumentSortHelper($this->get('router'), $request),
        ]);
    }

    /**
     * Proveedor de un proyecto.
     *
     * @Route("/project/{id}/provider/{provider}", name="project_provider")
     * @Security("has_role('ROLE_VIEW_PROVIDERS')")
     *
     * @param Persistence\Model\Project  $project
     * @param Persistence\Model\Provider $provider
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function providerAction(Project $project, Provider $provider)
    {
        $template = 'project/provider.html.twig';
        $providers = $project->getAllProviders();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $template = 'project/provider_content.html.twig';
        }

        return $this->render($template, [
            'area' => 'projects',
            'section' => 'providers',
            'project' => $project,
            'provider' => $provider,
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
        ]);
    }

    /**
     * Pesupuestos de un proyecto.
     *
     * @Route("/project/{id}/budgets", name="project_budgets")
     * @Security("has_role('ROLE_VIEW_BUDGETS')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function budgetsAction(Project $project)
    {
        return $this->render('project/budgets.html.twig', [
            'area' => 'projects',
            'section' => 'budgets',
            'project' => $project,
        ]);
    }

    /**
     * Pesupuesto de un proyecto.
     *
     * @Route("/project/{id}/budget/{provider}", name="project_budget")
     * @Security("has_role('ROLE_VIEW_BUDGETS')")
     *
     * @param Persistence\Model\Project  $project
     * @param Persistence\Model\Provider $provider
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function budgetAction(Project $project, Provider $provider)
    {
        if (! $project->hasProvider($provider)) {
            throw $this->createNotFoundException();
        }

        $providers = $project->getProvidersWithBudget();

        return $this->render('project/budget.html.twig', [
            'area' => 'projects',
            'section' => 'budgets',
            'project' => $project,
            'provider' => $provider,
            'budget' => $project->getBudget($provider),
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
        ]);
    }

    /**
     * Conversaciones de un proyecto.
     *
     * @Route("/project/{id}/conversations", name="project_conversations")
     * @Security("has_role('ROLE_VIEW_CONVERSATIONS')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function conversationsAction(Project $project)
    {
        $providers = $project->getAllProviders();
        $conversations = $this
            ->get('persistence.message_repository')
            ->findConversationsByProject($project)
        ;

        foreach ($conversations as $conversation) {
            foreach ($providers as $index => $provider) {
                if ($provider->getId() == $conversation['provider']->getId()) {
                    unset($providers[$index]);
                }
            }
        }

        return $this->render('project/conversations.html.twig', [
            'area' => 'projects',
            'section' => 'conversations',
            'project' => $project,
            'conversations' => $conversations,
            'without_conversation' => $providers,
        ]);
    }

    /**
     * Conversación de un proyecto.
     *
     * @Route("/project/{id}/messages/{provider}", name="project_messages")
     * @Security("has_role('ROLE_VIEW_CONVERSATIONS')")
     *
     * @param Persistence\Model\Project  $project
     * @param Persistence\Model\Provider $provider
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function messagesAction(Project $project, Provider $provider)
    {
        $providers = $this
            ->get('persistence.message_repository')
            ->distinctProvidersByProject($project)
        ;

        return $this->render('project/messages.html.twig', [
            'area' => 'projects',
            'section' => 'conversations',
            'project' => $project,
            'provider' => $provider,
            'project_provider' => $project->getProvider($provider),
            'previous' => $this->searchPrevious($provider, $providers),
            'next' => $this->searchNext($provider, $providers),
        ]);
    }

    /**
     * @param  Cursor  $providers
     * @param  Project $project
     * @return array
     */
    protected function sortByProject($providers, $project)
    {
        $providers = $providers->toArray();

        usort($providers, function ($a, $b) use ($project) {
            return $a->getWeight($project) < $b->getWeight($project);
        });

        return $providers;
    }
}
