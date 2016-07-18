<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Persistence\Model\User;
use Persistence\Model\Contact;

/**
 * Añade usuarios
 */
class UserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $required = InputOption::VALUE_REQUIRED;
        $optional = InputOption::VALUE_OPTIONAL;

        $this
            ->setName('neo:user:add')
            ->setDescription('Añade usuarios')
            ->addOption('name', null, $required, 'Nombre')
            ->addOption('email', null, $required, 'E-mail')
            ->addOption('role', null, $optional, 'Role', 'ROLE_MANAGER')
            ->addOption('password', null, $optional, 'Contraseña')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelper('dialog');
        $container = $this->getContainer();
        $dm = $container->get('doctrine.odm.mongodb.document_manager');
        $name = $input->getOption('name');
        $email = $input->getOption('email');
        $user = new User();
        $password = $input->getOption('password') ? : str_shuffle(uniqid());
        $role = $input->getOption('role');

        $output->writeln('Creando usuario tipo '.$role);

        if (! $name) {
            $name = $dialog->ask($output, 'Introduce el nombre: ');
        }

        if (! $email) {
            $email = $dialog->ask($output, 'Introduce el e-mail: ');
        }

        $contact = new Contact($name, null, null);
        $user
            ->setEmail($email)
            ->setPlainPassword($password)
            ->setEnabled(true)
            ->addRole($role)
            ->setContact($contact)
        ;

        $dm->persist($user);
        $dm->flush();

        $output->writeln(sprintf(
            'Creado usuario tipo %s con e-mail %s y contraseña %s',
            $role,
            $email,
            $password
        ));
    }
}
