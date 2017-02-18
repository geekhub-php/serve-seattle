<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\SurveyType;


class LoadSurveyTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $survey_type1 = new SurveyType();
        $survey_type1->setName('Internship Survey');

        $survey_type2 = new SurveyType();
        $survey_type2->setName('Speaker Survey');

        $survey_type3 = new SurveyType();
        $survey_type3->setName('End of year Survey');

        $manager->persist($survey_type1);
        $manager->persist($survey_type2);
        $manager->persist($survey_type3);
        $manager->flush();


        $this->addReference('survey_type1', $survey_type1);
        $this->addReference('survey_type2', $survey_type2);
        $this->addReference('survey_type3', $survey_type3);
    }

    public function getOrder()
    {
        return 3;
    }
}
