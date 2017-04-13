<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FormRequest;
use AppBundle\Entity\DTO\Filter;
use AppBundle\Form\DTO\FormRequestFilterType;
use AppBundle\Form\FormRequestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FormRequestController.
 *
 * @Route("/form_request", name="form_requests")
 */
class FormRequestController extends Controller
{
    /**
     * @Route("", name="form_request_list")
     * @Template("@App/request_forms.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = new Filter();
        $filterForm = $this->createForm(FormRequestFilterType::class, $filter)
            ->add('Search', SubmitType::class);

        $filterForm->handleRequest($request);
        $formRequests = $this->get('knp_paginator')->paginate(
            $em->getRepository(FormRequest::class)->selectRequestFormsByParams($filter),
            $request->query->getInt('page', 1),
            10
        );

        return [
            'formRequests' => $formRequests,
            'filterForm' => $filterForm->createView(),
        ];
    }

    /**
     * @Route("/form_request/approve", name="form_approve")
     *
     * @Method("PUT")
     *
     * @param Request     $request
     *
     * @return Response|false
     */
    public function activationAction(Request $request)
    {
        $status = $request->request->get('status');
        $id = $request->request->get('id');

        if (!$status || !$id) {
            return false;
        }

        $em = $this->getDoctrine()->getManager();
        $formRequest = $em->getRepository(FormRequest::class)->find($id);
        if (!$formRequest) {
            return false;
        }

        if ($status !== 'approved' && $status !== 'rejected') {
            return false;
        }

        $formRequest->setStatus($status);
        $em->flush();

        $this->get('app.email_notification')->sendNotification(
            $formRequest->getUser()->getEmail(),
            'Form request action',
            'Hello, '.$formRequest->getUser()->getFirstName().'.
            Your form request was '.$formRequest->getStatus().'.'
        );

         return new Response($status);
    }
}
