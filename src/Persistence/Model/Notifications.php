<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\Event;

/**
 * Modelo para la configuración de las notificaciones.
 *
 * @MongoDB\EmbeddedDocument
 */
class Notifications
{
    /**
     * @MongoDB\Boolean
     */
    protected $enabled;

    /**
     * @MongoDB\Collection
     */
    protected $enabledEvents;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->enabledEvents = $this->getEventsOptions();
    }

    /**
     * @return array
     */
    public static function getEventsOptions()
    {
        return [
            Event::PROVIDER,
            Event::PROJECT,
            Event::ACCEPT,
            Event::REJECT,
            Event::BUDGET,
            Event::MESSAGE,
        ];
    }

    /**
     * @return array
     */
    public static function getNamedEventsOptions()
    {
        return [
            Event::PROVIDER => 'Cada vez que se crea un proveedor',
            Event::PROJECT => 'Cada vez que se crea un proyecto',
            Event::ACCEPT => 'Cada vez que se acepta un proyecto',
            Event::REJECT => 'Cada vez que se rechaza un proyecto',
            Event::BUDGET => 'Cada vez que se envía un presupuesto',
            Event::MESSAGE => 'Cada vez que se envía un mensaje',
        ];
    }

    /**
     * @param boolean $enabled
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param array $enabledEvents
     * @return self
     */
    public function setEnabledEvents($enabledEvents)
    {
        $this->enabledEvents = $enabledEvents;

        return $this;
    }

    /**
     * @return array
     */
    public function getEnabledEvents()
    {
        return $this->enabledEvents;
    }

    /**
     * @param  string|Event $event
     * @return boolean
     */
    public function hasEnabledEvent($event)
    {
        $event = $event instanceof Event ? $event->getKind() : $event;

        return in_array($event, $this->getEnabledEvents());
    }
}
