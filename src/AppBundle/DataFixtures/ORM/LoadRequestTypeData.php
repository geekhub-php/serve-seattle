<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\RequestType;


class LoadRequestTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $request_type1 = new RequestType();
        $request_type1->setName('Personal Day Request');

        $request_type2 = new RequestType();
        $request_type2->setName('Overnight Guest Request');

        $request_type3 = new RequestType();
        $request_type3->setName('Sick Day Request');


        $manager->persist($request_type1);
        $manager->persist($request_type2);
        $manager->persist($request_type3);
        $manager->flush();


        $this->addReference('request_type1', $request_type1);
        $this->addReference('request_type2', $request_type2);
        $this->addReference('request_type3', $request_type3);
    }

    public function getOrder()
    {
        return 7;
    }
}
