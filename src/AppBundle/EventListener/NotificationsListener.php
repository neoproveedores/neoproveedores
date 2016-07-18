<?php

namespace AppBundle\EventListener;

use AppBundle\Event\UserEvent;
use Persistence\Model\User;
use Persistence\Model\Event;
use FOS\UserBundle\Util\TokenGenerator;

/**
 * Recibe evento y envía las notificaciones correspondientes
 */
class NotificationsListener
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Mailer          $mailer
     * @param Translator      $translator
     * @param UserRepository  $repository
     * @param LoggerInterface $logger
     */
    public function __construct($mailer, $translator, $repository, $logger)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param Event $event
     */
    public function onUserAction(UserEvent $event)
    {
        $event = $event->getEvent();
        $receivers = $this->findReceivers($event);
        $subject = $this->translator->trans('mail.event.'.$event->getKind());

        foreach ($receivers as $receiver) {
            $email = $receiver->getEmail();

            $this->logger->info(sprintf(
                'Email notification to %s: %s',
                $email,
                $subject
            ));

            $this->updateDisableNotificationsToken($receiver);
            $this->mailer->sendTemplate($email, $subject, 'event', [
                'receiver' => $receiver,
                'event' => $event,
            ]);
        }
    }

    /**
     * @param Event $event
     * @return array[User]
     */
    protected function findReceivers(Event $event)
    {
        if ($event->getKind() == Event::INVITE) {
            if ($user = $event->getProvider()->getUser()) {
                return [$user];
            }
        }

        $receivers = $this->findManagers($event);

        if ($project = $event->getProject()) {
            if ($user = $project->getAuthor()) {
                if ($user->hasRole('ROLE_PROJECT_MANAGER')) {
                    $receivers[] = $user;
                }
            }

            $receivers = array_merge($receivers, $this->findProviders($event));
        }

        return $receivers;
    }

    /**
     * @param Event $event
     * @return array[User]
     */
    protected function findManagers(Event $event)
    {
        return $this->repository
            ->findByRoleWithNotification('ROLE_MANAGER', $event->getKind())
            ->toArray()
        ;
    }

    /**
     * @param Event $event
     * @return array[User]
     */
    protected function findProviders(Event $event)
    {
        if ($event->getKind() == Event::MESSAGE) {
            $provider = $event->getMessage()->getProvider();

            if ($user = $provider->getUser()) {
                if ($user->getNotifications()->hasEnabledEvent($event)) {
                    return [$user];
                }
            }
        }

        return [];
    }

    /**
     * @param User $user
     */
    protected function updateDisableNotificationsToken(User $user)
    {
        if (! $user->getDisableNotificationsToken()) {
            $token = (new TokenGenerator())->generateToken();

            $user->setDisableNotificationsToken($token);
            $this->repository->save($user);
        }
    }
}
