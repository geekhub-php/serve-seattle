<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\FormRequest;
use AppBundle\Entity\FormRequestType;
use AppBundle\Entity\User;
use Mcfedr\JsonFormBundle\Controller\JsonController;
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
        $em = $this->getDoctrine()->getManager();
        $requestForms = $this->get('knp_paginator')
            ->paginate(
                $em->getRepository(FormRequest::class)->findBy([
                    'user' => $this->getUser(),
                    'status' => $status ? "pending" : ["approved","rejected"],
                ]),
                $request->query->getInt('page', 1),
                10
            );
        return $this->json($requestForms);
    }

    /**
     * @Route("/{formRequestType}/new")
     * @Method("POST")
     *
     * @param FormRequestType $formRequestType
     * @return JsonResponse
     */
    public function AddAction(FormRequestType $formRequestType)
    {
        $dt = '2017-12-12 12:25:23';
        if(preg_match('(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})', $dt)){
            $date = new \DateTime($dt);
        } else {
            return $this->json('Invalid date format');
        }
        $em = $this->getDoctrine()->getManager();
        $formRequest = new FormRequest();
        $formRequest->setDate($date)
            ->setType($formRequestType)
            ->setUser($this->getUser());

        $em->persist($formRequest);
        $em->flush();

        //TODO send email to admin
        return $this->json(true);
    }

}