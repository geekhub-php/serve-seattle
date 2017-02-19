<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\UserSupervisor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class LoadUserSupervisorData extends AbstractFixture implements OrderedFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $roles = array('ROLE_USER_SUPERVISOR');

        $user1 = new UserSupervisor();
        $user1->setUsername('supervisor1');
        $user1->setFirstName($faker->name);
        $user1->setEmail($faker->email);
        $user1->setAddress($faker->streetAddress);
        $user1->setPhoneNumber($faker->phoneNumber);
        $user1->setDateOfBirth($faker->dateTime);
        $user1->setPassword('supervisor1');
        $user1->setRoles($roles);
        $manager->persist($user1);

        $user2 = new UserSupervisor();
        $user2->setUsername('supervisor2');
        $user2->setFirstName($faker->name);
        $user2->setEmail($faker->email);
        $user2->setAddress($faker->streetAddress);
        $user2->setPhoneNumber($faker->phoneNumber);
        $user2->setDateOfBirth($faker->dateTime);
        $user2->setPassword('supervisor2');
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
