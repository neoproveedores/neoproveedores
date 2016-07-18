<?php

namespace Persistence\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Persistence\Model\Provider;
use Persistence\Model\Contact;
use Persistence\Model\Amount;
use Persistence\Model\Metrics;
use Persistence\Model\Skill;
use Persistence\Model\Address;
use Persistence\Model\Rating;
use Persistence\Model\CompetenceRating;
use Persistence\Model\File;
use Persistence\Model\User;

/**
 * Provedores de ejemplo.
 */
class ProviderFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $dm)
    {
        foreach ($this->getNames() as $index => $name) {
            $provider = new Provider();
            $address = new Address(
                '1 Stokholm St',
                'San Francisco',
                'CA',
                '94198',
                'USA'
            );

            $provider
                ->setStatus(Provider::ACTIVE)
                ->setContact($this->createRandomContact($name))
                ->setHourRate(new Amount(rand(35, 75)))
                ->setMetrics(new Metrics(rand(2, 4), rand(1, 6), rand(0, 2), rand(0, 2)))
                ->setSkills($this->chooseRandomSkills())
                ->setContacts($this->createRandomContacts())
                ->setAddress($address)
                ->setNotes(file_get_contents(__DIR__.'/Resources/lorem.txt'))
                ->setUser($this->createUser($dm, $provider))
            ;
            $this->createRandomRatings($dm, $provider);

            if ($provider->getContact()->getBusinessName()) {
                $provider->setKind(Provider::COMPANY);
            }

            $dm->persist($provider);
            $this->addReference('provider-'.$index, $provider);
        }

        $dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @param  ObjectManager $dm
     * @param  Provider        $provider
     * @return User
     */
    protected function createUser(ObjectManager $dm, Provider $provider)
    {
        $user = new User();

        $user
            ->setEmail($provider->getContact()->getEmail())
            ->setPlainPassword('interacso')
            ->setEnabled(true)
            ->addRole('ROLE_PROVIDER')
            ->setProvider($provider)
        ;

        $dm->persist($user);

        return $user;
    }

    /**
     * @param int $number
     *
     * @return array
     */
    protected function chooseRandomSkills($number = 2)
    {
        $skills = [];

        foreach (array_rand(AbilityFixtures::names(), $number) as $index) {
            $name = AbilityFixtures::names()[$index];
            $ability = $this->getReference('ability-'.$name);
            $skill = new Skill($ability, rand(35, 75), rand(2, 4));
            $skills[] = $skill;

            $skill
                ->setMetrics(new Metrics(rand(2, 4), rand(1, 6), rand(0, 2), rand(0, 2)))
                ->setNotes(file_get_contents(__DIR__.'/Resources/lorem.txt'))
                ->setFiles($this->createRandomFiles(rand(1, 3)))
            ;
        }

        return $skills;
    }

    /**
     * @param int $number
     *
     * @return array
     */
    protected function createRandomContacts($number = 3)
    {
        $contacts = [];

        for ($c = 0; $c < $number; ++$c) {
            $contacts[] = $this->createRandomContact();
        }

        return $contacts;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function createRandomContact($name = null)
    {
        if (!$name) {
            $names = $this->getNames();
            $name = $names[array_rand($names)];
        }

        $contact = new Contact($name[0], $name[1], $name[2]);
        $contact->setPhone('415-486-480'.rand(0, 9));
        $contact->setAlternatePhone('415-486-480'.rand(0, 9));
        $contact->setEmail($name[3]);

        return $contact;
    }

    /**
     * @param DocumentManadetr $dm
     * @param Provider         $provider
     * @param int              $number
     *
     * @return array
     */
    protected function createRandomRatings($dm, $provider, $number = 7)
    {
        $ratings = [];
        $user = $this->getReference('manager');
        $notes = file_get_contents(__DIR__.'/Resources/lorem.txt');

        for ($i = 0; $i < $number; ++$i) {
            $competences = [];

            foreach (CompetenceFixtures::names() as $name) {
                $reference = $this->getReference('competence-'.$name);
                $competences[] = new CompetenceRating($reference, rand(0, 5));
            }

            $rating = new Rating($competences, null, $provider, $user);
            $rating->setNotes($notes);
            $dm->persist($rating);
            $ratings[] = $rating;
        }

        return $ratings;
    }

    /**
     * @param int $number
     *
     * @return array
     */
    protected function createRandomFiles($number = 3)
    {
        $files = [];
        $names = [
            'Fichero muy importante.zip',
            'Fichero vital.zip',
            'Fichero crítico.zip',
            'Fichero secreto.zip',
        ];
        $selected = array_rand($names, $number);
        $selected = is_array($selected) ? $selected : [$selected];

        foreach ($selected as $index) {
            $file = new File();
            $file->setName($names[$index])->setSize(rand(128, 4096));
            $files[] = $file;
        }

        return $files;
    }

    /**
     * @return array
     */
    protected function getNames()
    {
        return [
            ['Bebidas Solano', null, null, 'providertest11@devhosting2.eu'],
            ['Canonistas del Sur', null, null, 'providertest12@devhosting2.eu'],
            ['Ana', 'Herrero', 'Directora de Proveedores', 'aherrero@dodepecho.com'],
            ['Miguel', 'Arana', 'Directora de Proyectos', 'miguel@dodepecho.com'],
            ['Sonia', 'Fonts', 'Cuentas', 'sonia@dodepecho.com'],
            ['Óscar', 'del Río', 'Gestor', 'oscar.delrio@interacso.com'],
            ['Miguel', 'Exposito', 'Gestor', 'mijel.exposito@gmail.com'],
            ['Producciones Jorge y María', null, null, 'providertest13@devhosting2.eu'],
            ['Estudio de Diseño CG', null, null, 'providertest14@devhosting2.eu'],
            ['Imprenta Torres', null, null, 'providertest15@devhosting2.eu'],
        ];
    }
}
