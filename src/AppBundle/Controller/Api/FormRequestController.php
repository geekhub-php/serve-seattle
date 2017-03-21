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
     * @Route("/")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function pendingListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pending = $this->get('knp_paginator')
            ->paginate(
                $em->getRepository(FormRequest::class)->findBy(['user' => $this->getUser()]),
                $request->query->getInt('page', 1),
                10
            );
        return $this->json($pending);
    }

}