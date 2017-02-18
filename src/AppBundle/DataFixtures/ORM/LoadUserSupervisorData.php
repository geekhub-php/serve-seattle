<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\UserSupervisor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class LoadUserSupervisorData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

        $roles = array('ROLE_USER_SUPERVISOR');

        $user1 = new UserSupervisor();
        $user1->setUsername($faker->name);
        $user1->setFirstName($faker->name);
        $user1->setEmail($faker->email);
        $user1->setAddress($faker->streetAddress);
        $user1->setPhoneNumber($faker->phoneNumber);
        $user1->setDateOfBirth($faker->dateTime);
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user1, $faker->word);
        $user1->setPassword($password);
        $user1->setRoles($roles);
        $manager->persist($user1);

        $user2 = new UserSupervisor();
        $user2->setUsername($faker->name);
        $user2->setFirstName($faker->name);
        $user2->setEmail($faker->email);
        $user2->setAddress($faker->streetAddress);
        $user2->setPhoneNumber($faker->phoneNumber);
        $user2->setDateOfBirth($faker->dateTime);
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user2, $faker->word);
        $user2->setPassword($password);
        $user2->setRoles($roles);
        $manager->persist($user2);
        $manager->flush();
        $this->addReference('supervisor1', $user1);
        $this->addReference('supervisor2', $user2);
    }

    public function getOrder()
    {
        return 1;
    }
}
