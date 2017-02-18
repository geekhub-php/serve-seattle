<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Survey;


class LoadSurveyData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $survey1 = new Survey();
        $survey1->setType($this->getReference('survey_type1'));
        $survey1->setIntern($this->getReference('intern1'));


        $manager->persist($survey1);

        $manager->flush();

        $this->addReference('survey1', $survey1);

    }

    public function getOrder()
    {
        return 4;
    }
}
