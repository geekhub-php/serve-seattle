<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use AppBundle\Entity\UserIntern;

class LoadUserInternData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $roles = array('ROLE_USER_INTERN');
        $user1 = new UserIntern();
        $user1->setUsername('intern1');
        $user1->setFirstName($faker->name);
        $user1->setEmail($faker->email);
        $user1->setAddress($faker->streetAddress);
        $user1->setPhoneNumber($faker->phoneNumber);
        $user1->setDateOfBirth($faker->dateTime);
        $user1->setSupervisor($this->getReference('supervisor1'));
        $user1->setPassword('intern1');
        $user1->setRoles($roles);
        $manager->persist($user1);

        $user2 = new UserIntern();
        $user2->setUsername('intern2');
        $user2->setFirstName($faker->name);
        $user2->setEmail($faker->email);
        $user2->setAddress($faker->streetAddress);
        $user2->setPhoneNumber($faker->phoneNumber);
        $user2->setDateOfBirth($faker->dateTime);
        $user2->setSupervisor($this->getReference('supervisor1'));
        $user2->setPassword('intern2');
        $user2->setRoles($roles);
        $manager->persist($user2);

        $user3 = new UserIntern();
        $user3->setUsername('intern3');
        $user3->setFirstName($faker->name);
        $user3->setEmail($faker->email);
        $user3->setAddress($faker->streetAddress);
        $user3->setPhoneNumber($faker->phoneNumber);
        $user3->setDateOfBirth($faker->dateTime);
        $user3->setSupervisor($this->getReference('supervisor1'));
        $user3->setPassword('intern3');
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
