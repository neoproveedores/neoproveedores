<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Default controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $route = 'fos_user_security_login';

        if ($this->isGranted('ROLE_MANAGER')) {
            $route = 'timeline';
        } else if ($this->isGranted('ROLE_USER')) {
            $route = 'projects';
        }

        return $this->redirectToRoute($route);
    }
}
