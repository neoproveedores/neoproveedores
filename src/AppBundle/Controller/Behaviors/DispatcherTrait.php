<?php

namespace AppBundle\Controller\Behaviors;

/**
 * Facilita el envío de eventos
 */
trait DispatcherTrait
{
    /**
     * @param string $name
     * @param object $event
     */
    protected function dispatch($name, $event)
    {
        return $this->get('event_dispatcher')->dispatch($name, $event);
    }
}
