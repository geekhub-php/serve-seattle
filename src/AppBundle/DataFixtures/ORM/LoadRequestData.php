<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Request;


class LoadRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request1 = new Request();
        $request1->setType($this->getReference('request_type1'));
        $request1->setIntern($this->getReference('intern1'));

        $request2 = new Request();
        $request2->setType($this->getReference('request_type2'));
        $request2->setIntern($this->getReference('intern1'));

        $request3 = new Request();
        $request3->setType($this->getReference('request_type3'));
        $request3->setIntern($this->getReference('intern2'));

        $manager->persist($request1);
        $manager->persist($request2);
        $manager->persist($request3);


        $manager->flush();



    }

    public function getOrder()
    {
        return 8;
    }
}
