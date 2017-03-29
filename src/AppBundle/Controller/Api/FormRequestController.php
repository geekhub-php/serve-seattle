<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\FormRequest;
use AppBundle\Entity\FormRequestType;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
        $em = $this->getDoctrine()->getManager();
        $requestForms = $this->get('knp_paginator')
            ->paginate(
                $em->getRepository(FormRequest::class)->findBy([
                    'user' => $this->getUser(),
                    'status' => $status == "pending" ? "pending" : ["approved", "rejected"],
                ]),
                $request->query->getInt('page', 1),
                10
            );
        return $this->json($requestForms);
    }

    /**
     * @Route("/{type}")
     * @Method("POST")
     *
     * @param FormRequestType $type
     * @param  Request $request
     * @return JsonResponse
     */
    public function addAction(FormRequestType $type, Request $request)
    {
        if (!$request->getContent()) {
            throw new JsonHttpException(404, 'Request body is empty');
        }

        $data = json_decode($request->getContent());

        if (!preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $data->date)) {
            throw new JsonHttpException(404, 'Invalid date format');
        }

        $date = new \DateTime($data->date);
        $em = $this->getDoctrine()->getManager();
        $formRequest = new FormRequest();
        $formRequest->setDate($date)
            ->setType($type)
            ->setUser($this->getUser());

        $em->persist($formRequest);
        $em->flush();

        return $this->json("Request form created", 200);
    }
}
