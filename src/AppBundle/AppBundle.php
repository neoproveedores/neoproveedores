<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle for application specific code.
 */
class AppBundle extends Bundle
{
    /**
     * Declara el bundle como hijo de FOSUserBundle para sobrescribir algunas
     * de sus funciones.
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
