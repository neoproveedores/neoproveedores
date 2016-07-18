<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Persistence\Model\User;
use Persistence\Model\Event as Action;

/**
 * Event de acción de usuario
 */
class UserEvent extends Event
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Event
     */
    protected $event;

    /**
     * @param User  $user
     * @param Event $event
     */
    public function __construct(User $user, Action $event)
    {
        $this->user = $user;
        $this->event = $event;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
