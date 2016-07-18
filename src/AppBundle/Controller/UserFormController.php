<?php

namespace AppBundle\Controller;

use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\CreateUserType;
use AppBundle\Form\UserType;
use Persistence\Model\ChangePassword;
use Persistence\Model\User;
use Persistence\Model\Provider;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controlador de formularios para usuario
 */
class UserFormController extends Controller
{
    /**
     * @Route("/create/user", name="user_create")
     * @Security("has_role('ROLE_CREATE_USER')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $user = new User();
        $user->addRole('ROLE_MANAGER');

        $form = $this->createForm(new CreateUserType(), $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            if ($user->getRole() == 'ROLE_PROVIDER') {
                $provider = new Provider();
                $provider->setContact($user->getContact());
                $user->setContact(null);
                $user->setProvider($provider);
            }

            $user->setEnabled(true);
            $dm->persist($user);
            $dm->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('user/form/data.html.twig', [
            'form' => $form->createView(),
            'area' => 'users',
            'action' => 'Nuevo',
        ]);
    }

    /**
     * @Route("/edit/user/{id}", name="edit_user")
     * @Security("has_role('ROLE_EDIT_USER')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Persistence\Model\User                   $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, User $user)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $form = $this->createForm(new UserType(), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $dm->persist($user);
            $dm->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('user/form/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'area' => 'users',
            'action' => 'Editar',
        ]);
    }

    /**
     * @Route("/change/password/{id}", name="change_password")
     * @Security("has_role('ROLE_EDIT_USER')")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Persistence\Model\User                   $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changePasswordAction(Request $request, User $user)
    {
        $changePassword = new ChangePassword();
        $form = $this->createForm(new ChangePasswordType(), $changePassword);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $dm = $this->get('doctrine.odm.mongodb.document_manager');
            $user->setPlainPassword($changePassword->getNewPassword());
            $dm->persist($user);
            $dm->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('user/form/password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'area' => 'users',
            'action' => 'Editar',
        ]);
    }

    /**
     * @Route("/delete/user/{id}", name="delete_user")
     *
     * @param \Persistence\Model\User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(User $user)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $dm->remove($user);
        $dm->flush();

        return $this->redirectToRoute('users');
    }
}
