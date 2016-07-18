<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Persistence\Model\Event;
use Persistence\Model\User;

/**
 * Controlador para probar eventos
 */
class EventController extends Controller
{
    use Behaviors\MongoDBTrait;

    /**
     * Eventos no vistos.
     *
     * @Route("/unview/events", name="unview_events")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function unviewAction()
    {
        $unview = $this
            ->get('persistence.event_repository')
            ->findLastWithoutUser($this->getUser())
        ;

        return $this->render('timeline/unview.html.twig', [
            'unview' => $unview,
        ]);
    }

    /**
     * Desactiva las notificaciones
     *
     * @Route(
     *     "/disable/notifications/{disableNotificationsToken}",
     *     name="disable_notifications"
     * )
     *
     * @param FOS\UserBundle\Model\User $user
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function disableNotificationsAction(User $user)
    {
        $user->getNotifications()->setEnabled(false);
        $this->saveDocument($user);

        return $this->render('message.html.twig', [
            'area' => 'nope',
            'icon' => 'mail',
            'title' => 'Notificaciones desactivadas',
            'body' => 'Puedes volver a activarlas desde tu panel de usuario',
        ]);
    }

    /**
     * Muestra la plantilla de email para un evento
     *
     * @Route("/mail/event/{id}", name="mail_event")
     *
     * @param Event $event
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function mailAction(Event $event)
    {
        $receiver = $this
            ->get('persistence.user_repository')
            ->findOneByRole('ROLE_MANAGER')
        ;

        $receiver->setDisableNotificationsToken('olaqase');
        $this->saveDocument($receiver);

        return $this->render('mail/event.html.twig', [
            'receiver' => $receiver,
            'event' => $event,
        ]);
    }
}
