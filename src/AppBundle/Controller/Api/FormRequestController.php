<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\FormRequest;
use Doctrine\Common\Collections\Criteria;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @Route("/form-request", name="form_requests")
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
                    'status' => $status == 'pending' ? 'pending' : ['approved', 'rejected'],
                ], ['createdAt' => Criteria::DESC]),
                $request->query->getInt('page', 1),
                10
            );

        return $this->json(['requestForms' => $requestForms]);
    }

    /**
     * @Route("/{type}", requirements={"type"= "sick-day|personal-day|overnight-guest"})
     * @Method("POST")
     *
     * @param $type
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction($type, Request $request)
    {
        if (!$request->getContent()) {
            throw new JsonHttpException(404, 'Request body is empty');
        }
        $em = $this->getDoctrine()->getManager();

        $formRequest = new FormRequest();
        $formRequest
            ->setType(str_replace('-', ' ', $type))
            ->setUser($this->getUser());

        $formRequest = $this->get('serializer')->deserialize(
            $request->getContent(),
            FormRequest::class,
            'json',
            ['object_to_populate' => $formRequest]
        );

        if (is_null($formRequest->getDate())) {
            throw new JsonHttpException(404, 'Date is required');
        }

        $errors = $this->get('validator')->validate($formRequest);
        if ($errors->count()) {
            $outErrors = [];
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $outErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new JsonHttpException(400, 'Bad Request', $outErrors);
        }
        $em->persist($formRequest);
        $em->flush();

        $this->get('app.email_notification')->sendNotification(
            $formRequest->getUser()->getEmail(),
            'Form request',
            'Hello, '.$formRequest->getUser()->getFirstName().'. Your form request was created.'
        );

        return $this->json([
            'success' => [
                'code' => 200,
                'message' => 'Request form created.',
            ],
        ], 200);
    }
}
