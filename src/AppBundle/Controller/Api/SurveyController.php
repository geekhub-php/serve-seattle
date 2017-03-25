<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Survey\Survey;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class SurveyController extends Controller
{
    /**
     * @Route("/surveys", name="list_surveys")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function listAction()
    {
        return $this->json(
            ['surveys' => $this->getDoctrine()->getRepository(Survey::class)->findSurveyByUser($this->getUser())], 200
        );
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
        if ($survey->getStatus() == 'submited') {
            return $this->json(['survey' => $survey, 'answers' => $survey->getAnswers()]);
        }
        return $this->json($survey);
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
            'json',
            array('object_to_populate' => $survey, 'groups' => array('group4'))
        );
        if (!$survey) {
            throw new JsonHttpException(404, 'Not valid Survey');
        }
        $em->persist($survey);
        $em->flush();

        return $this->json(['message' => 'Survey is updated'], 200);
    }
}
