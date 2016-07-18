<?php

namespace AppBundle\EventListener;

use Symfony\Component\Routing\Router;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Persistence\Model\Provider;

/**
 * Crea un proveedor vacío tras el registro de usuario.
 */
class RegistrationListener
{
    /**
     * @var Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @param Symfony\Component\Routing\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param  FormEvent $event
     */
    public function onRegistrationInitialize(GetResponseUserEvent $event)
    {
        $user = $event->getUser();

        $user->setProvider(new Provider())->addRole('ROLE_PROVIDER');
    }

    /**
     * @param  FilterUserResponseEvent $event
     */
    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $response = $event->getResponse();
        $url = $this->router->generate('provider_edit_data', [
            'id' => $user->getProvider()->getId(),
        ]);

        $response->setTargetUrl($url);
    }
}
