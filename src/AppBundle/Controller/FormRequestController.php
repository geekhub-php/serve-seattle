<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FormRequest;
use AppBundle\Entity\User;
use AppBundle\Entity\DTO\Filter;
use AppBundle\Form\DTO\FormRequestFilterType;
use AppBundle\Form\User\EditType;
use AppBundle\Form\User\ActivationType;
use AppBundle\Form\FormRequestType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FormRequestController
 * @package AppBundle\Controller
 * @Route("/form_request", name="form_requests")
 */
class FormRequestController extends Controller
{
    /**
     * @Route("", name="form_request_list")
     * @Template("@App/FormRequest/list.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = new Filter;
        $filterForm = $this->createForm(FormRequestFilterType::class, $filter)
            ->add('Search', SubmitType::class);

        $filterForm->handleRequest($request);
        $formRequests = $this->get('knp_paginator')->paginate(
            $em->getRepository(FormRequest::class)->selectRequestFormsByParams($filter),
            $request->query->getInt('page', 1),
            10
        );
        $approveForms =[];

        foreach ($formRequests as $formRequest) {
            if ($formRequest->getStatus() == "pending") {
                $approveForms[$formRequest->getId()] = $this->createForm(FormRequestType::class, $formRequest, [
                    'method' => "PUT",
                    'action' => $this->generateUrl('form_approve', ['id' => $formRequest->getId()]),
                ])
                    ->createView();
            }
        }
        return [
            'formRequests' => $formRequests,
            'approveForms' => $approveForms,
            'filterForm' => $filterForm->createView(),
        ];
    }

    /**
     * @Route("/form_request/approve/{id}", name="form_approve")
     *
     * @Method("PUT")
     * @param  Request $request
     * @param  FormRequest $formRequest
     * @return RedirectResponse
     */
    public function activationAction(Request $request, FormRequest $formRequest)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(FormRequestType::class, $formRequest, [
            'method' => "PUT",
            'action' => $this->generateUrl('form_approve', ['id' => $formRequest->getId()]),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($formRequest);
                $em->flush();
                //TODO send email to intern
//                $message = \Swift_Message::newInstance()
//                    ->setSubject('Hello Email')
//                    ->setFrom('send@example.com')
//                    ->setTo('recipient@example.com')
//                    ->setBody(
//                        $this->renderView(
//                        // app/Resources/views/Emails/registration.html.twig
//                            'Emails/registration.html.twig',
//                            array('name' => $name)
//                        ),
//                        'text/html'
//                    )
//                    /*
//                     * If you also want to include a plaintext version of the message
//                    ->addPart(
//                        $this->renderView(
//                            'Emails/registration.txt.twig',
//                            array('name' => $name)
//                        ),
//                        'text/plain'
//                    )
//                    */
//                ;
//                $this->get('mailer')->send($message);
            }
        }

        return $this->redirect($this->generateUrl("form_request_list"));
    }
}