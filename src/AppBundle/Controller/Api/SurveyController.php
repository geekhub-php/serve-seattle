<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Survey\SurveyAnswer;
use AppBundle\Entity\Survey\Survey;
use AppBundle\Entity\Survey\SurveyQuestion;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class SurveyController extends JsonController
{
    /**
     * @Route("/surveys", name="list_surveys")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function listAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $surveys = $em->getRepository(Survey::class)->findSurveyByUser($user);
        $serializer = $this->get('serializer');
        $json = $serializer->normalize(
            $surveys,
            null,
            array('groups' => array('group1'))
        );

        return $this->json(['surveys' => $json], 200);
    }

    /**
     * @param int $id
     * @Route("/surveys/{id}", name="show_survey")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function showAction($id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($id);
        if ($user !== $survey->getUser()) {
            throw new JsonHttpException(403, "Current user doesn't have accesses to this resource");
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

        return $this->json($jsonSurvey);
    }

    /**
     * @param Request $request, int $id
     * @Route("/surveys/{id}", name="edit_survey")
     * @Method("PUT")
     *
     * @return JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($id);
        if ($user !== $survey->getUser() || $survey->getStatus() === 'submited') {
            throw new JsonHttpException(403, "Current user can't change this resource");
        }
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
