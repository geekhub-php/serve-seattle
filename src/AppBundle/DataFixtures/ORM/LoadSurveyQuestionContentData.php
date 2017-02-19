<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\SurveyQuestionContent;


class LoadSurveyQuestionContentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $survey_quest_content1 = new SurveyQuestionContent();
        $survey_quest_content1->setSurveyType($this->getReference('survey_type1'));
        $survey_quest_content1->setContent('How many hours do you work?');

        $survey_quest_content2 = new SurveyQuestionContent();
        $survey_quest_content2->setSurveyType($this->getReference('survey_type1'));
        $survey_quest_content2->setContent('How well did internship meet your expectation?');

        $survey_quest_content3 = new SurveyQuestionContent();
        $survey_quest_content3->setSurveyType($this->getReference('survey_type2'));
        $survey_quest_content3->setContent('Did you feel equipped by what your Supervisor gave you?');

        $manager->persist($survey_quest_content1);
        $manager->persist($survey_quest_content2);
        $manager->persist($survey_quest_content3);
        $manager->flush();


        $this->addReference('survey_quest_content1', $survey_quest_content1);
        $this->addReference('survey_quest_content2', $survey_quest_content2);
        $this->addReference('survey_quest_content3', $survey_quest_content3);
    }

    public function getOrder()
    {
        return 5;
    }
}
