<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Persistence\Model\Project;
use Persistence\Model\ProjectProvider;
use Persistence\Model\Provider;
use Persistence\Model\Rating;
use Persistence\Model\Event;
use AppBundle\Form\ProjectType;
use AppBundle\Form\RejectionType;
use AppBundle\Form\BudgetType;
use AppBundle\Form\RatingType;
use AppBundle\AppEvents;
use AppBundle\Event\UserEvent;

/**
 * Controlador para formularios de proyectos.
 */
class ProjectFormController extends Controller
{
    use Behaviors\MongoDBTrait;

    /**
     * Crea un proyecto nuevo.
     *
     * @Route("/create/project", name="project_create")
     * @Security("has_role('ROLE_CREATE_PROJECT')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $project = new Project();

        $project->setAuthor($this->getUser());
        $dm->persist($project);
        $dm->flush();

        return $this->redirectToRoute('project_edit', [
            'area' => 'projects',
            'id' => $project->getId(),
        ]);
    }

    /**
     * Edita un proyecto existente.
     *
     * @Route("/edit/project/{id}", name="project_edit")
     * @Security("has_role('ROLE_EDIT_PROJECT')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Project $project)
    {
        if (! $project->isEditable()) {
            throw new BadRequestHttpException('No se puede editar el proyecto');
        }

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $form = $this->createForm(new ProjectType($dm), $project);
        $downloadUrl = $this->generateUrl('download_project', [
            'id' => $project->getId(),
            'index' => 'index',
        ]);
        $route = 'project_briefing';

        $form->handleRequest($this->getRequest());
        $project->clearEmptyFiles();

        if ($form->isSubmitted() && $form->isValid()) {
            $dm = $this->get('doctrine.odm.mongodb.document_manager');

            if ($project->hasStatus(Project::DRAFT)) {
                // TODO: ¿Crear un estado intermedio entre draft y sent?
                $route = 'project_providers';
                $project->setStatus(Project::SENT);

                $user = $this->getUser();
                $event = new Event(Event::PROJECT, $user, $project);
                $this->saveDocument($event);
                $this->get('event_dispatcher')->dispatch(
                    AppEvents::USER_ACTION,
                    new UserEvent($user, $event)
                );
            }

            $dm->persist($project);
            $dm->flush();

            return $this->redirectToRoute($route, [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('project/form/edit.html.twig', [
            'area' => 'projects',
            'project' => $project,
            'form' => $form->createView(),
            'download_url' => $downloadUrl,
        ]);
    }

    /**
     * Invita a proveedores a un proyecto
     *
     * @Route("/invite/to/project/{id}", name="project_invite")
     * @Security("has_role('ROLE_SEND_INVITATIONS')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction(Project $project)
    {
        $providers = [];
        $user = $this->getUser();
        $request = $this->getRequest();
        $repository = $this->get('persistence.provider_repository');

        foreach ($request->get('providers') as $id) {
            if ($provider = $repository->find($id)) {
                $providers[] = $provider;
                $project->addProvider($provider);
            }
        }

        $project->setStatus(Project::SENT);
        $this->saveDocument($project);

        foreach ($providers as $provider) {
            $event = new Event(Event::INVITE, $user, $project, $provider);
            $this->saveDocument($event);
            $this->get('event_dispatcher')->dispatch(
                AppEvents::USER_ACTION,
                new UserEvent($user, $event)
            );
        }

        if ($request->isXmlHttpRequest()) {
            return new Response(null, 201);
        }

        return $this->redirectToRoute('project_providers', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * Acepta la invitación a un proyecto.
     *
     * @Route("/accept/project/{id}", name="project_accept")
     * @Security("has_role('ROLE_ACCEPT_PROJECT') and project.hasProvider(user.getProvider())")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function acceptAction(Project $project)
    {
        $user = $this->getUser();
        $provider = $user->getProvider();
        $projectProvider = $project->getProvider($provider);

        $projectProvider->setStatus(ProjectProvider::ACCEPTED);
        $this->saveDocument($project);

        $event = new Event(Event::ACCEPT, $user, $project, $provider);
        $this->saveDocument($event);
        $this->get('event_dispatcher')->dispatch(
            AppEvents::USER_ACTION,
            new UserEvent($user, $event)
        );

        return $this->redirectToRoute('project_briefing', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * Reachaza la invitación a un proyecto.
     *
     * @Route("/reject/project/{id}", name="project_reject")
     * @Security("has_role('ROLE_REJECT_PROJECT') and project.hasProvider(user.getProvider())")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function rejectAction(Project $project)
    {
        $user = $this->getUser();
        $provider = $user->getProvider();
        $form = $this->createForm(new RejectionType());
        $projectProvider = $project->getProvider($provider);

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() and $form->isValid()) {
            $projectProvider->setStatus(ProjectProvider::REJECTED);
            $projectProvider->setRejection($form->getData());
            $this->saveDocument($project);

            $event = new Event(Event::REJECT, $user, $project, $provider);
            $this->saveDocument($event);
            $this->get('event_dispatcher')->dispatch(
                AppEvents::USER_ACTION,
                new UserEvent($user, $event)
            );

            return new Response(null, 201);
        }

        return new Response($form->getErrorsAsString(), 400);
    }

    /**
     * Crea un pesupuesto para un proyecto.
     *
     * @Route("/project/{id}/create/budget", name="project_create_budget")
     * @Security("has_role('ROLE_SEND_BUDGET')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createBudgetAction(Project $project)
    {
        $user = $this->getUser();
        $provider = $user->getProvider();
        $form = $this->createForm(new BudgetType());
        $projectProvider = $project->getProvider($provider);

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $budget = $form->getData();

            $budget->clearEmptyFiles();
            $project->setStatus(Project::RECEIVING);
            $projectProvider->setStatus(ProjectProvider::BUDGETED);
            $projectProvider->setBudget($budget);
            $this->saveDocument($project);

            $event = new Event(Event::BUDGET, $user, $project, $provider);
            $this->saveDocument($event);
            $this->get('event_dispatcher')->dispatch(
                AppEvents::USER_ACTION,
                new UserEvent($user, $event)
            );

            return $this->redirectToRoute('project_briefing', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('project/form/budget.html.twig', [
            'area' => 'projects',
            'section' => null,
            'project' => $project,
            'form' => $form->createView(),
            'project_provider' => $projectProvider,
        ]);
    }

    /**
     * Modifica el pesupuesto para un proyecto.
     *
     * @Route("/project/{id}/edit/budget", name="project_edit_budget")
     * @Security("has_role('ROLE_EDIT_BUDGET')")
     *
     * @param Persistence\Model\Project $project
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editBudgetAction(Project $project)
    {
        if ($project->isAssigned()) {
            throw new BadRequestHttpException('El proyecto ya está asignado');
        }

        $user = $this->getUser();
        $provider = $user->getProvider();
        $projectProvider = $project->getProvider($provider);
        $budget = $projectProvider->getBudget();
        $form = $this->createForm(new BudgetType(), $budget);
        $event = new Event(Event::UPDATE_BUDGET, $user, $project, $provider);

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $budget->clearEmptyFiles();
            $this->saveDocument($project);

            $this->saveDocument($event);
            $this->get('event_dispatcher')->dispatch(
                AppEvents::USER_ACTION,
                new UserEvent($user, $event)
            );

            return $this->redirectToRoute('project_briefing', [
                'id' => $project->getId(),
            ]);
        }

        return $this->render('project/form/budget.html.twig', [
            'area' => 'projects',
            'section' => null,
            'project' => $project,
            'form' => $form->createView(),
            'project_provider' => $projectProvider,
            'budget' => $budget,
        ]);
    }

    /**
     * Asigna un proyecto a un proveedor.
     *
     * @Route("/assign/project/{id}/to/{provider}", name="project_assign")
     * @Security("has_role('ROLE_ASSIGN_PROJECT') and not project.isAssigned()")
     *
     * @param Persistence\Model\Project  $project
     * @param Persistence\Model\Provider $provider
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function assignAction(Project $project, Provider $provider)
    {
        $provider = $project->getProvider($provider);

        $project->setStatus(Project::ASSIGNED);
        $provider->setStatus(ProjectProvider::ASSIGNED);
        $provider->setAssignation(new \DateTime());
        $this->saveDocument($project);

        return $this->redirectToRoute('project_briefing', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * Valora el trabajo de un proveedor en un proyecto.
     *
     * @Route("/rate/project/{id}", name="project_rating")
     * @Security("has_role('ROLE_RATE_PROJECT')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function rateAction(Project $project)
    {
        $provider = $project->getAssignedProvider();
        $rating = $this
            ->get('persistence.rating_factory')
            ->createEmpty($project, $provider, $this->getUser())
        ;
        $form = $this->createForm(new RatingType(), $rating);

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() and $form->isValid()) {
            $project->setStatus(Project::CLOSED);
            $this->saveDocument($rating);
            $this->saveDocument($project);

            return $this->redirectToRoute('projects');
        }

        return $this->render('project/form/rating.html.twig', [
            'area' => 'projects',
            'section' => null,
            'project' => $project,
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/close/project/{id}", name="project_close")
     * @Security("has_role('ROLE_CLOSE_PROJECT')")
     *
     * @param Persistence\Model\Project $project
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function closeAction(Project $project)
    {
        $project->setStatus(Project::CLOSED);

        $this->saveDocument($project);

        return $this->redirectToRoute('project_briefing', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @Route("/remove/project/{id}", name="project_remove")
     * @Security("has_role('ROLE_REMOVE_PROJECT')")
     *
     * @param Persistence\Model\Project $project
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Project $project)
    {
        $this->getRepository('event')->removeByProject($project);
        $this->getRepository('message')->removeByProject($project);
        $this->getRepository('rating')->removeByProject($project);

        $this->removeDocument($project);

        return $this->render('message.html.twig', [
            'area' => 'nope',
            'icon' => 'trash',
            'title' => 'Proyecto eliminado',
            'body' => '',
        ]);
    }
}
