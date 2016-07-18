<?php

namespace TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\AppEvents;
use AppBundle\Event\UserEvent;
use Persistence\Model\Event;
use Persistence\Model\Message;
use Persistence\Model\Contact;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Controller\Behaviors;

/**
 * Test controller
 */
class DefaultController extends Controller
{
    use Behaviors\MongoDBTrait;

    /**
     * @Route("/test", name="test")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testAction()
    {
        $managers = $this
            ->get('persistence.user_repository')
            ->findByRole('ROLE_MANAGER')
        ;
        $projectManagers = $this
            ->get('persistence.user_repository')
            ->findByRole('ROLE_PROJECT_MANAGER')
        ;
        $providers = $this->get('persistence.provider_repository')->findAll();

        return $this->render('default/index.html.twig', [
            'managers' => $managers,
            'project_managers' => $projectManagers,
            'providers' => $providers,
        ]);
    }

    /**
     * @Route("/test/manager/{id}", name="test_manager")
     *
     * @param null|string $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testManagerAction($id = null)
    {
        $repository = $this->get('persistence.user_repository');
        $user = $id ? $repository->find($id) : $repository->findFirst();

        $this->signAsUser($user);

        return $this->redirectToRoute('timeline');
    }

    /**
     * @Route("/test/provider/{id}", name="test_provider")
     *
     * @param string $id
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testProviderAction($id = null)
    {
        $repository = $this->get('persistence.provider_repository');
        $provider = $id ? $repository->find($id) : $repository->findFirst();

        $this->signAsUser($provider->getUser());

        return $this->redirectToRoute('projects');
    }

    /**
     * @Route("/test/event/provider")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testEventProviderAction()
    {
        $repository = $this->get('persistence.provider_repository');
        $provider = $repository->findFirst();
        $user = $this->getUser();

        $event = new Event(Event::PROVIDER, $user, null, $provider);
        $this->saveDocument($event);
        $this->get('event_dispatcher')->dispatch(
            AppEvents::USER_ACTION,
            new UserEvent($user, $event)
        );

        return new Response('👍');
    }

    /**
     * @Route("/test/mail/{kind}")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @param string $kind
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testMailProviderAction($kind)
    {
        $mailer = $this->get('app.mailer');
        $repository = $this->get('persistence.event_repository');
        $events = $repository->findByKind($kind);

        if (! $events->count()) {
            throw $this->createNotFoundException();
        }

        $data = [
            'receiver' => $this->getUser(),
            'event' => $events->getNext(),
        ];

        if ($to = $this->getRequest()->get('email')) {
            $mailer->sendTemplate($to, 'Mensaje de prueba', 'event', $data);
        }

        return $this->render('mail/event.html.twig', $data);
    }

    /**
     * @Route("/test/event/project")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testEventProjectAction()
    {
        $repository = $this->get('persistence.project_repository');
        $project = $repository->findPublic()->getNext();
        $user = $this->getUser();

        $event = new Event(Event::PROJECT, $user, $project);
        $this->saveDocument($event);
        $this->get('event_dispatcher')->dispatch(
            AppEvents::USER_ACTION,
            new UserEvent($user, $event)
        );

        return new Response('👍');
    }

    /**
     * @Route("/test/event/accept")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testEventAcceptAction()
    {
        $repository = $this->get('persistence.provider_repository');
        $provider = $repository->findFirst();
        $repository = $this->get('persistence.project_repository');
        $project = $repository->findByProvider($provider)->getNext();
        $user = $this->getUser();

        $event = new Event(Event::ACCEPT, $user, $project, $provider);
        $this->saveDocument($event);
        $this->get('event_dispatcher')->dispatch(
            AppEvents::USER_ACTION,
            new UserEvent($user, $event)
        );

        return new Response('👍');
    }

    /**
     * @Route("/test/event/reject")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testEventRejectAction()
    {
        $repository = $this->get('persistence.provider_repository');
        $provider = $repository->findFirst();
        $repository = $this->get('persistence.project_repository');
        $project = $repository->findByProvider($provider)->getNext();
        $user = $this->getUser();

        $event = new Event(Event::REJECT, $user, $project, $provider);
        $this->saveDocument($event);
        $this->get('event_dispatcher')->dispatch(
            AppEvents::USER_ACTION,
            new UserEvent($user, $event)
        );

        return new Response('👍');
    }

    /**
     * @Route("/test/event/message")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function testEventMessageAction()
    {
        $repository = $this->get('persistence.provider_repository');
        $provider = $repository->findFirst();
        $repository = $this->get('persistence.project_repository');
        $project = $repository->findByProvider($provider)->getNext();
        $user = $this->getUser();
        $message = new Message($project, $provider);

        $message->setAuthor($user);
        $message->setBody('ola q ase');
        $this->saveDocument($message);

        $event = new Event(Event::MESSAGE, $user, $project, $provider);
        $event->setMessage($message);
        $this->saveDocument($event);
        $this->get('event_dispatcher')->dispatch(
            AppEvents::USER_ACTION,
            new UserEvent($user, $event)
        );

        return new Response('👍');
    }

    protected function createUser($role, $provider = null)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $user->setEmail(uniqid().'@interacso.com');
        $user->setPlainPassword('interacso');
        $user->setEnabled(true);
        $user->addRole($role);
        $user->setProvider($provider);
        if (! $provider) {
            $position = 'Lord Comandante de la Guardia de la Noche';
            $user->setContact(new Contact('Jon', 'Snow', $position));
        }
        $userManager->updateUser($user);

        return $user;
    }

    protected function signAsUser($user)
    {
        $token = new UsernamePasswordToken(
            $user,
            $user->getPassword(),
            'main',
            $user->getRoles()
        );
        $this->get('security.context')->setToken($token);
    }
}
