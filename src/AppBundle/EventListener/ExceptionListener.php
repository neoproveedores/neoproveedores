<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Escucha exceptiones para mostrar pantallas de error menos intimidantes
 */
class ExceptionListener
{
    /**
     * @var Symfony\Bundle\TwigBundle\TwigEngine
     */
    protected $templating;

    /**
     * @param Symfony\Bundle\TwigBundle\TwigEngine $templating
     */
    public function __construct($templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof HttpException) {
            $view = $this->templating->render('error.html.twig', [
                'area' => 'error',
                'exception' => $exception,
            ]);

            $event->setResponse(new Response($view));
        }
    }
}
