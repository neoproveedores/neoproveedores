<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controlador para mostrar los eventos en orden cronológino
 */
class TimelineController extends Controller
{
    use Behaviors\MongoDBTrait;

    /**
     * @Route("/timeline", name="timeline")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (! $this->isGranted('ROLE_VIEW_EVENTS')) {
            return $this->redirectToRoute('projects');
        }

        $user = $this->getUser();
        $repository = $this->get('persistence.event_repository');
        $events = $repository->findAfter(new \DateTime('-1 month'));
        $paginator = $this->get('app.document_paginator');
        $pagination = $paginator->paginate($events, $request->get('skip'));

        $template = 'timeline/index.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'timeline/events.html.twig';
        }

        $user->setLastTimelineVisit(new \DateTime());
        $this->saveDocument($user);

        return $this->render($template, [
            'area' => 'timeline',
            'pagination' => $pagination,
            'next_url' => $paginator->generateNextUrl($request, $pagination),
        ]);
    }
}
