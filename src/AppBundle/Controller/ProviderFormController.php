<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Persistence\Model\Event;
use Persistence\Model\Provider;
use Persistence\Model\Billing;
use AppBundle\Form\ProviderDataType;
use AppBundle\Form\ProviderContactsType;
use AppBundle\Form\ProviderBillingType;
use AppBundle\Form\ProviderSkillsType;
use AppBundle\Form\ProviderSkillsManagerType;
use AppBundle\Form\ProviderNotesType;
use AppBundle\AppEvents;
use AppBundle\Event\UserEvent;
use Persistence\Validator\InvalidProviderException;

/**
 * Controlador de formularios para proveedores
 */
class ProviderFormController extends Controller
{
    use Behaviors\MongoDBTrait;

    /**
     * @Route("/create/provider", name="provider_create")
     * @Security("has_role('ROLE_CREATE_PROVIDER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $provider = new Provider();

        $dm->persist($provider);
        $dm->flush();

        return $this->redirectToRoute('provider_edit_data', [
            'id' => $provider->getId(),
        ]);
    }

    /**
     * @Route("/edit/provider/{id}/data", name="provider_edit_data")
     * @Security("has_role('ROLE_EDIT_PROVIDER') and provider.hasUser(user)")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function dataAction(Provider $provider)
    {
        $form = $this->createForm(new ProviderDataType(), $provider);

        return $this->handleForm($form, 'data', 'contacts');
    }

    /**
     * Editar los contactos de un proveedor
     *
     * @Route("/edit/provider/{id}/contacts", name="provider_edit_contacts")
     * @Security("has_role('ROLE_EDIT_PROVIDER') and provider.hasUser(user)")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function contactsAction(Provider $provider)
    {
        $form = $this->createForm(new ProviderContactsType(), $provider);

        return $this->handleForm($form, 'contacts', 'billing');
    }

    /**
     * Editar los datos de pago de un proveedor
     *
     * @Route("/edit/provider/{id}/billing", name="provider_edit_billing")
     * @Security("has_role('ROLE_EDIT_PROVIDER') and provider.hasUser(user)")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function billingAction(Provider $provider)
    {
        $form = $this->createForm(new ProviderBillingType(), $provider);

        return $this->handleForm($form, 'billing', 'skills');
    }

    /**
     * Editar las habilidades de un proveedor
     *
     * @Route("/edit/provider/{id}/skills", name="provider_edit_skills")
     * @Security("has_role('ROLE_EDIT_PROVIDER') and provider.hasUser(user)")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function skillsAction(Provider $provider)
    {
        if ($provider->hasSkillsClosed()) {
            return $this->render('provider/form/skills_closed.html.twig', [
                'area' => 'providers',
                'section' => 'skills',
                'provider' => $provider,
            ]);
        }

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $type = new ProviderSkillsType($dm);
        if ($this->getUser()->hasRole('ROLE_MANAGER')) {
            $type = new ProviderSkillsManagerType($dm);
        }
        $form = $this->createForm($type, $provider);

        return $this->handleForm($form, 'skills', 'notes');
    }

    /**
     * Editar las notas de un proveedor
     *
     * @Route("/edit/provider/{id}/notes", name="provider_edit_notes")
     * @Security("has_role('ROLE_CREATE_PROVIDER')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function notesAction(Provider $provider)
    {
        $form = $this->createForm(new ProviderNotesType(), $provider);

        return $this->handleForm($form, 'notes', 'finish');
    }

    /**
     * @Route("/edit/provider/{id}/finish", name="provider_edit_finish")
     * @Security("has_role('ROLE_CREATE_PROVIDER')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function finishAction(Provider $provider)
    {
        if ($provider->hasStatus(Provider::DRAFT)) {
            $this->validate($provider);

            $provider->setStatus(Provider::ACTIVE);
            $this->saveDocument($provider);

            $this->createUser($provider);

            $user = $this->getUser();
            $event = new Event(Event::PROVIDER, $user, null, $provider);
            $this->saveDocument($event);
            $this->get('event_dispatcher')->dispatch(
                AppEvents::USER_ACTION,
                new UserEvent($user, $event)
            );
        }

        return $this->redirectToRoute('provider_preview', [
            'id' => $provider->getId(),
        ]);
    }

    /**
     * @Route("/remove/provider/{id}", name="remove_provider")
     * @Security("has_role('ROLE_REMOVE_PROVIDER')")
     *
     * @param Persistence\Model\Provider $provider
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Provider $provider)
    {
        $projects = $this->getRepository('project')->findByProvider($provider);

        foreach ($projects as $project) {
            $project->removeProvider($provider);
            $this->saveDocument($project);
        }

        $this->getRepository('event')->removeByProvider($provider);
        $this->getRepository('message')->removeByProvider($provider);
        $this->getRepository('rating')->removeByProvider($provider);

        if ($user = $provider->getUser()) {
            $this->removeDocument($user);
        }
        $this->removeDocument($provider);

        return $this->render('message.html.twig', [
            'area' => 'nope',
            'icon' => 'trash',
            'title' => 'Proveedor eliminado',
            'body' => '',
        ]);
    }

    /**
     * @param Symfony\Component\Form\FormTypeInterface $form
     * @param string $template
     * @param string $route Si el formulario es válido
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function handleForm($form, $template, $route)
    {
        $user = $this->getUser();
        $provider = $form->getData();
        $original = clone $provider;
        $form->handleRequest($this->getRequest());

        if ($billing = $provider->getBilling()) {
            $billing->clearEmptyFiles();
        }
        foreach ($provider->getSkills() as $skill) {
            $skill->clearEmptyFiles();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $dm = $this->get('doctrine.odm.mongodb.document_manager');
            
            $dm->persist($provider);
            $dm->flush();

            if ($provider->hasStatus(Provider::DRAFT)) {
                if ($route == 'notes' && $user->hasRole('ROLE_PROVIDER')) {
                    return $this->redirectToRoute('projects');
                } else {
                    $route = 'provider_edit_'.$route;
                }
            } else {
                if ($user->hasRole('ROLE_PROVIDER')) {
                    return $this->redirectToRoute('projects');
                } else {
                    $route = 'provider_preview';
                }
            }

            return $this->redirectToRoute($route, [
                'id' => $provider->getId(),
            ]);
        }

        $abilities = $this->get('persistence.ability_repository')->findAll();

        return $this->render("provider/form/$template.html.twig", [
            'area' => 'providers',
            'section' => $template,
            'provider' => $provider,
            'abilities' => $abilities,
            'original' => $original,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param  Provider $provider
     * @return User
     */
    protected function createUser(Provider $provider)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $password = uniqid();

        $user->setEmail($provider->getContact()->getEmail());
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $user->addRole('ROLE_PROVIDER');
        $user->setProvider($provider);
        $userManager->updateUser($user);

        $this->get('app.mailer')->sendTemplate(
            $user->getEmail(),
            'Has sido añadido como proveedor',
            'provider/register',
            ['user' => $user, 'password' => $password]
        );

        return $user;
    }

    /**
     * @param  Provider $provider [description]
     * @throws InvalidProviderException
     */
    protected function validate(Provider $provider)
    {
        $groups = [];
        $isValid = true;
        $keys = ['data', 'contacts', 'billing', 'skills'];

        foreach ($keys as $key) {
            $errors = $this->validateGroup($provider, 'provider_'.$key);
            $groups[] = [
                'url' => $this->generateFormUrl($provider, $key),
                'errors' => $errors,
            ];

            if (count($errors)) {
                $isValid = false;
            }
        }

        if (! $isValid) {
            throw new InvalidProviderException($groups);
        }
    }

    /**
     * @param  Provider $provider
     * @param  string   $route
     * @return string
     */
    protected function generateFormUrl(Provider $provider, $route)
    {
        return $this->generateUrl('provider_edit_'.$route, [
            'id' => $provider->getId(),
        ]);
    }

    /**
     * @param  Provider $provider
     * @param  string   $group
     * @return ConstraintViolationListInterface
     */
    protected function validateGroup(Provider $provider, $group)
    {
        return $this->get('validator')->validate($provider, null, [$group]);
    }
}
