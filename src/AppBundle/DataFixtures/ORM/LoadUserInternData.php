<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use AppBundle\Entity\UserIntern;

class LoadUserInternData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $roles = array('ROLE_USER_INTERN');
        $user1 = new UserIntern();
        $user1->setUsername($faker->name);
        $user1->setFirstName($faker->name);
        $user1->setEmail($faker->email);
        $user1->setAddress($faker->streetAddress);
        $user1->setPhoneNumber($faker->phoneNumber);
        $user1->setDateOfBirth($faker->dateTime);
        $user1->setSupervisor($this->getReference('supervisor1'));
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user1, $faker->word);
        $user1->setPassword($password);
        $user1->setRoles($roles);
        $manager->persist($user1);

        $user2 = new UserIntern();
        $user2->setUsername($faker->name);
        $user2->setFirstName($faker->name);
        $user2->setEmail($faker->email);
        $user2->setAddress($faker->streetAddress);
        $user2->setPhoneNumber($faker->phoneNumber);
        $user2->setDateOfBirth($faker->dateTime);
        $user2->setSupervisor($this->getReference('supervisor1'));
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user2, $faker->word);
        $user2->setPassword($password);
        $user2->setRoles($roles);
        $manager->persist($user2);

        $user3 = new UserIntern();
        $user3->setUsername($faker->name);
        $user3->setFirstName($faker->name);
        $user3->setEmail($faker->email);
        $user3->setAddress($faker->streetAddress);
        $user3->setPhoneNumber($faker->phoneNumber);
        $user3->setDateOfBirth($faker->dateTime);
        $user3->setSupervisor($this->getReference('supervisor1'));
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user3, $faker->word);
        $user3->setPassword($password);
        $user3->setRoles($roles);
        $manager->persist($user3);



        $manager->flush();

        $this->addReference('intern1', $user1);
        $this->addReference('intern2', $user2);
        $this->addReference('intern3', $user3);
    }

    public function getOrder()
    {
        return 2;
    }
}
