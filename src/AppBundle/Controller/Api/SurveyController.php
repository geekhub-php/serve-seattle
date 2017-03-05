<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyQuestion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SurveyController extends Controller
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

        $json = $serializer->serialize(
            $surveys,
            'json', array('groups' => array('group1'))
        );
        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Survey $survey
     * @Route("/survey/{id}", name="api_survey_byid")
     * @Method("GET")
     * @ParamConverter("survey", class="AppBundle:Survey")
     */
    public function apiSurveyAction(Survey $survey)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'User is not authorized'], 401);
        }
        $em = $this->getDoctrine()->getManager();

        if (!$survey) {
            return $this->json(['message' => 'No surveys'], 404);
        }
        $serializer = $this->get('serializer');
        if ($survey->getStatus() == 'submited') {
            $json = $serializer->serialize(
                $survey,
                'json', array('groups' => array('group1', 'group2'))
            );
        } else {
            $json = $serializer->serialize(
                $survey,
                'json', array('groups' => array('group1', 'group3'))
            );
        }

        $response = new Response($json, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Request $request, Survey $survey
     * @Route("/survey/update/{id}", name="api_survey_update")
     * @Method("POST")
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
        foreach ($survey->getType()->getQuestions() as $question) {
            $quest_key[] = $question->getId();
        }
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        foreach ($quest_key as $key) {
            $answer = $data[$key];
            if ($answer == null) {
                return $this->json(['message' => 'Wrong question id'], 400);
            }
            $new_answer = new SurveyAnswer();
            $new_answer->setSurvey($survey);
            $new_answer->setQuestion($em->getRepository(SurveyQuestion::class)->find($key));
            $new_answer->setContent($answer);
            $em->persist($new_answer);
        }
        $survey->setStatus('submited');
        $em->flush();

        return $this->json(['message' => 'Survey updated'], 200);
    }
}
