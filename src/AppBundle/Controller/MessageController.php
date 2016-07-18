<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Persistence\Model\Project;
use Persistence\Model\Provider;
use Persistence\Model\Message;
use Persistence\Model\Event;
use AppBundle\Form\MessageType;
use AppBundle\AppEvents;
use AppBundle\Event\UserEvent;

/**
 * Controlador para mensajes
 */
class MessageController extends Controller
{
    use Behaviors\MongoDBTrait;

    /**
     * Mensajes no leídos (ítem del menu principal)
     *
     * @Route("/unread/messages", name="messages_unread")
     * @Security("has_role('ROLE_VIEW_MESSAGES')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function unreadAction()
    {
        $unread = $this
            ->get('persistence.message_repository')
            ->findUnreadBy($this->getUser())
        ;

        return $this->render('message/unread.html.twig', [
            'unread' => $unread,
        ]);
    }

    /**
     * Mensajes no leídos (ítem del menu principal)
     *
     * @Route("/project/{id}/unread/messages", name="project_messages_unread")
     * @Security("has_role('ROLE_VIEW_MESSAGES')")
     *
     * @param Persistence\Model\Project $project
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function projectUnreadAction(Project $project)
    {
        $template = '<span class="ui red circular mini label">%s</span>';
        $unread = $this
            ->get('persistence.message_repository')
            ->countUnreadByProject($project, $this->getUser())
        ;

        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonResponse([
                'unread' => $unread,
            ]);
        }

        if ($unread) {
            return new Response(sprintf($template, $unread));
        }

        return new Response();
    }

    /**
     * Mensajes sobre un proyecto para el gestor del proyecto
     *
     * @Route("/messages/{id}/{provider}", name="messages_provider")
     * @Security("has_role('ROLE_VIEW_MESSAGES')")
     *
     * @param Persistence\Model\Project  $project
     * @param Persistence\Model\Provider $provider
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function providerAction(Project $project, Provider $provider)
    {
        return $this->handleMessage($project, $provider);
    }

    /**
     * Mensajes sobre un proyecto para el proveedor
     *
     * @Route("/messages/{id}", name="messages_project")
     * @Security("has_role('ROLE_VIEW_MESSAGES')")
     *
     * @param Persistence\Model\Project $project
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function projectAction(Project $project)
    {
        return $this->handleMessage($project, $this->getUser()->getProvider());
    }

    /**
     * @param Persistence\Model\Project  $project
     * @param Persistence\Model\Provider $provider
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function handleMessage(Project $project, Provider $provider)
    {
        $user = $this->getUser();
        $message = new Message($project, $provider);
        $form = $this->createForm(new MessageType(), $message);
        $repository = $this->get('persistence.message_repository');

        $form->handleRequest($this->getRequest());

        if ($form->isSubmitted() and $form->isValid()) {
            $message = $form->getData();

            $message->setAuthor($user);
            $this->saveDocument($message);

            $event = new Event(Event::MESSAGE, $user, $project, $provider);
            $event->setMessage($message);
            $this->saveDocument($event);
            $this->get('event_dispatcher')->dispatch(
                AppEvents::USER_ACTION,
                new UserEvent($user, $event)
            );

            $message = new Message($project, $provider);
            $form = $this->createForm(new MessageType(), $message);
        }

        $messages = $repository->findByProjectAndProvider($project, $provider);
        $repository->markAsReadBy($user, $messages);

        return $this->render('message/index.html.twig', [
            'project' => $project,
            'provider' => $provider,
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }
}
