<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Survey\Survey;
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
        $data = $request->getContent();
        $survey = $em->getRepository(Survey::class)->find($id);

        if ($user !== $survey->getUser()) {
            throw new JsonHttpException(403, "Current user can't change this resource");
        }
        if ('current' !== $survey->getStatus()) {
            throw new JsonHttpException(404, 'Survey was already submited');
        }
        $serializer = $this->get('serializer');

        $survey = $serializer->deserialize(
            $data,
            Survey::class,
            'json', array('object_to_populate' => $survey, 'groups' => array('group4'))
        );
        if (!$survey) {
            throw new JsonHttpException(404, 'Not valid Survey');
        }
        $em->persist($survey);
        $em->flush();

        return $this->json(['message' => 'Survey updated'], 200);
    }
}
