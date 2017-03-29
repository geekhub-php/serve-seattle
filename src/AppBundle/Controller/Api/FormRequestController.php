<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\FormRequest;
use AppBundle\Entity\FormRequestType;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/form_request", name="form_requests")
 */
class FormRequestController extends JsonController
{
    /**
     * @Route("/{status}", requirements={"status": "pending|past"})
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function listAction(Request $request, $status)
    {
        $requestForms = $this->get('knp_paginator')
            ->paginate(
                $this->getDoctrine()->getManager()->getRepository(FormRequest::class)->findBy([
                    'user' => $this->getUser(),
                    'status' => $status == "pending" ? "pending" : ["approved", "rejected"],
                ]),
                $request->query->getInt('page', 1),
                10
            );
        return $this->json($requestForms);
    }

    /**
     * @Route("/{type}", requirements={"type"= "sick-day|personal-day|overnight-guest"})
     * @Method("POST")
     *
     * @param FormRequestType $type
     * @param  Request $request
     * @return JsonResponse
     */
    public function addAction($type, Request $request)
    {
        if (!$request->getContent()) {
            throw new JsonHttpException(404, 'Request body is empty');
        }

        $data = json_decode($request->getContent());

        if (!preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $data->date)) {
            throw new JsonHttpException(404, 'Invalid date format');
        }

        $em = $this->getDoctrine()->getManager();

        switch ($type){
            case 'sick-day':
                $formRequestType = $em->getRepository(FormRequestType::class)
                    ->findOneBy(['name' => 'Sick Day']);
                break;
            case 'personal-day':
                $formRequestType = $em->getRepository(FormRequestType::class)
                    ->findOneBy(['name' => 'Personal Day']);
                break;
            case 'overnight-guest':
                $formRequestType = $em->getRepository(FormRequestType::class)
                    ->findOneBy(['name' => 'Overnight Guest']);
                break;
        }
        $date = new \DateTime($data->date);
        $formRequest = new FormRequest();
        $formRequest->setDate($date)
            ->setType($formRequestType)
            ->setUser($this->getUser());
        $em->persist($formRequest);
        $em->flush();

        $this->get('app.email_notification')->sendNotification(
            $formRequest->getUser()->getEmail(),
            "Form request",
            "Hello, ".$formRequest->getUser()->getFirstName().". Your form request was created."
        );
        return $this->json("Request form created", 200);
    }
}
