<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Survey\SurveyAnswer;
use AppBundle\Entity\Survey\SurveyType;
use AppBundle\Entity\Survey\Survey;
use AppBundle\Entity\DTO\Filter;
use AppBundle\Form\DTO\SurveyFilterType;
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
     *
     * @return array
     */
    public function surveysAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filter = new Filter();
        $filterForm = $this->createForm(SurveyFilterType::class, $filter);
        $filterForm->handleRequest($request);

        $surveys = $em->getRepository(Survey::class)->selectSurveysByParams($filter);
        $types = $em->getRepository(SurveyType::class)->selectSurveyTypesByParams($filter);
        $surveyTypes = $em->getRepository(SurveyType::class)->findAll();

        return [
            'surveys' => $this->get('knp_paginator')
                ->paginate(
                    $surveys,
                    $request->query->getInt('page', 1),
                    20
                ),
            'types' => $types,
            'filterForm' => $filterForm->createView(),
            'surveyTypes' => $surveyTypes
        ];
    }

    /**
     * @param Survey $survey
     * @Route("/surveys/{id}", name="survey")
     * @Template("@App/survey.html.twig")
     * @ParamConverter("survey", class="AppBundle\Entity\Survey\Survey")
     *
     * @return array
     */
    public function surveyAction(Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        if ($survey->isReviewed() == false) {
            $survey->setReviewed(true);
            $em->persist($survey);
            $em->flush();
        }
        $answers = $em->getRepository(SurveyAnswer::class)->findAnswersBySurvey($survey);
        if ($answers) {
            foreach ($answers as $answer) {
                $questions[] = $answer->getQuestion()->getId();
                $contents[] = $answer->getContent();
            }
            $questionAnswer = array_combine($questions, $contents);
        }
        if (!$answers) {
            $questionAnswer = null;
        }

        return [
            'survey' => $survey, 'question_answers' => $questionAnswer,
        ];
    }

    /**
     * @param Request $request, SurveyType $surveyType
     * @Route("/surveys/create/{survey_type}", name="survey_create")
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

            return $this->redirectToRoute('surveys');
        }

        return $this->render('@App/survey.html.twig', array(
            'form' => $form->createView(), 'survey' => $survey, 'question_answers' => null
        ));
    }

    /**
     * @param Request $request, Survey $survey
     * @Route("/surveys/delete/{id}", requirements={"id": "\d+"}, name="survey_delete")
     * @ParamConverter("survey", class="AppBundle\Entity\Survey\Survey")
     */
    public function surveyDeleteAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getManager();
        $answers = $em->getRepository(SurveyAnswer::class)->findAnswersBySurvey($survey);
        if ($answers) {
            foreach ($answers as $answer) {
                $questions[] = $answer->getQuestion()->getId();
                $contents[] = $answer->getContent();
            }
            $questionAnswer = array_combine($questions, $contents);
        }
        if (!$answers) {
            $questionAnswer = null;
        }
        $form = $this->createForm(\AppBundle\Form\SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($survey);
            $em->flush();

            return $this->redirectToRoute('surveys');
        }

        return $this->render('@App/survey.html.twig', array(
            'form' => $form->createView(), 'survey' => $survey, 'question_answers' => $questionAnswer,
        ));
    }
}
