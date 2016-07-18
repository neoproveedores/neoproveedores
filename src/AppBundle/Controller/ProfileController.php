<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ProfileType;
use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\NotificationsType;
use AppBundle\Form\NotificationsManagerType;

/**
 * Controlador del perfil de usuarios
 */
class ProfileController extends Controller
{
    /**
     * Modificar el perfil del usuario
     *
     * @Route("/update/profile", name="update_profile")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(new ProfileType(), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user);

            return $this->redirectToRoute('projects');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'area' => 'profile',
        ]);
    }

    /**
     * Modificar la contraseña del usuario
     *
     * @Route("/update/password", name="update_password")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function passwordAction(Request $request)
    {
        $user = $this->getUser();
        $formFactory = $this->get('fos_user.change_password.form.factory');
        $form = $formFactory->createForm();

        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user);

            return $this->redirectToRoute('projects');
        }

        return $this->render('profile/password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'area' => 'profile',
        ]);
    }

    /**
     * Modificar la configuración de las notificaciones
     *
     * @Route("/manage/notifications", name="manage_notifications")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function notificationsAction(Request $request)
    {
        $user = $this->getUser();
        $notifications = $user->getNotifications();
        $type = new NotificationsType();
        if ($user->hasRole('ROLE_MANAGER')) {
            $type = new NotificationsManagerType();
        }
        $form = $this->createForm($type, $notifications);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user);

            return $this->redirectToRoute('projects');
        }

        return $this->render('profile/notifications.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'area' => 'profile',
        ]);
    }
}
