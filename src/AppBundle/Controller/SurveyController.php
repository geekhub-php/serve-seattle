<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SurveyController extends Controller
{
    /**
     * @Route("/surveys", name="surveys")
     * @Template("@App/surveys.html.twig")
     */
    public function surveysAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $surveys = $em->getRepository(Survey::class)->findSurveyByStatus('submited');
        $surveyTypes = $em->getRepository(SurveyType::class)->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($surveys, $request->query->getInt('page', 1), 10);

        return [
            'surveys' => $pagination, 'survey_types' => $surveyTypes,
        ];
    }

    /**
     * @param Survey $survey
     * @Route("/survey/{id}", name="survey_get")
     * @Template("@App/survey.html.twig")
     * @ParamConverter("survey", class="AppBundle:Survey")
     */
    public function surveyAction(Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        $fields = $em->getRepository(SurveyAnswer::class)->findAnswersBySurvey($survey);

        return [
            'survey' => $survey, 'fields' => $fields,
        ];
    }

    /**
     * @param Request $request, SurveyType $surveyType
     * @Route("/survey/create/{survey_type}", name="survey_create")
     * @ParamConverter("surveyType", options={"mapping": {"survey_type": "name"}})
     */
    public function surveyCreateAction(Request $request, SurveyType $surveyType)
    {
        $survey = new Survey();
        $survey->setType($surveyType);
        $form = $this->createForm(\AppBundle\Form\SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/surveyform.html.twig', array(
            'form' => $form->createView(), 'type' => $surveyType,
        ));
    }

    /**
     * @param Request $request, Survey $survey
     * @Route("/survey/delete/{id}", name="survey_delete")
     * @ParamConverter("survey", class="AppBundle:Survey")
     */
    public function surveyDeleteAction(Request $request, Survey $survey)
    {
        $surveyType = $survey->getType();

        $form = $this->createForm(\AppBundle\Form\SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($survey);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/surveyform.html.twig', array(
            'form' => $form->createView(), 'type' => $surveyType,
        ));
    }
}
