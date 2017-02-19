<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\SurveyQuestion;


class LoadSurveyQuestionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $question1 = new SurveyQuestion();
        $question1->setSurvey($this->getReference('survey1'));
        $question1->setIntern($this->getReference('intern1'));
        $question1->setQuestionContent($this->getReference('survey_quest_content1'));
        $question1->setAnswer('Yes');

        $question2 = new SurveyQuestion();
        $question2->setSurvey($this->getReference('survey1'));
        $question2->setIntern($this->getReference('intern1'));
        $question2->setQuestionContent($this->getReference('survey_quest_content2'));
        $question2->setAnswer('No');


        $manager->persist($question1);
        $manager->persist($question2);

        $manager->flush();


    }

    public function getOrder()
    {
        return 6;
    }
}
