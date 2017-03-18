<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyQuestion;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class SurveyController extends JsonController
{
    /**
     * @Route("/surveys", name="api_surveys")
     * @Method("GET")
     */
    public function apiSurveysAction()
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'User is not authorized'], 401);
        }
        $em = $this->getDoctrine()->getManager();
        $surveys = $em->getRepository(Survey::class)->findSurveyByUser($user);
        if (!$surveys) {
            return $this->json(['message' => 'No surveys'], 404);
        }
        $serializer = $this->get('serializer');

        $json = $serializer->normalize(
            $surveys,
            null,
            array('groups' => array('group1'))
        );

        return $this->json(['surveys' => $json], 200);
    }

    /**
     * @param Survey $survey
     * @Route("/surveys/{id}", name="api_survey")
     * @Method("GET")
     * @ParamConverter("survey", class="AppBundle:Survey")
     */
    public function apiSurveyAction(Survey $survey)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'User is not authorized'], 401);
        }

        if (!$survey) {
            return $this->json(['message' => 'No surveys'], 404);
        }
        $serializer = $this->get('serializer');
        $jsonSurvey = $serializer->normalize(
            $survey,
            null,
            array('groups' => array('group1', 'group2'))
        );
        if ($survey->getStatus() == 'submited') {
            $answers = $survey->getAnswers();
            $jsonAnswers = $serializer->normalize(
                $answers,
                null,
                array('groups' => array('group3'))
            );

            return $this->json(['survey' => $jsonSurvey, 'answers' => $jsonAnswers], 200);
        }

        return $this->json(['survey' => $jsonSurvey], 200);
    }

    /**
     * @param Request $request, Survey $survey
     * @Route("/surveys/{id}", name="api_survey_update")
     * @Method("PUT")
     * @ParamConverter("survey", class="AppBundle:Survey")
     */
    public function apiSurveyUpdateAction(Request $request, Survey $survey)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'User is not authorized'], 401);
        }
        if (!$survey || $survey->getStatus() == 'submited') {
            return $this->json(['message' => 'No survey'], 404);
        }
        $questKey = array();
        foreach ($survey->getType()->getSections() as $section) {
            foreach ($section->getQuestions() as $question) {
                $questKey[] = $question->getId();
            }
        }
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        foreach ($questKey as $key) {
            $answer = $data[$key];
            if ($answer == null) {
                return $this->json(['message' => 'Survey is not filled out'], 400);
            }
            $question = $em->getRepository(SurveyQuestion::class)->find($key);
            $variants = $question->getVariants();
            if (count($variants) > 0 & !in_array($answer, $variants)) {
                return $this->json(['message' => 'Wrong answer variant.'], 400);
            }
            $newAnswer = new SurveyAnswer();
            $newAnswer->setSurvey($survey);
            $newAnswer->setQuestion($question);
            $newAnswer->setContent($answer);
            $em->persist($newAnswer);
        }
        $survey->setStatus('submited');
        $em->flush();

        return $this->json(['message' => 'Survey updated'], 200);
    }
}
