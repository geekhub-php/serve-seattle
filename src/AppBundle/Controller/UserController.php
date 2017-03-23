<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\DTO\Filter;
use AppBundle\Exception\JsonHttpException;
use AppBundle\Form\FilterType;
use AppBundle\Form\User\EditType;
use AppBundle\Form\User\RegistrationType;
use AppBundle\Form\User\ActivationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users_list")
     * @Template("@App/users.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = new Filter;
        $filterForm = $this->createForm(FilterType::class, $filter)
            ->add("Search", SubmitType::class, array(
                "attr" => array("class" => "fa fa-search")
            ));
        $filterForm->handleRequest($request);
        $users = $this->get('knp_paginator')->paginate(
            $em->getRepository(User::class)->selectUsersByParams($filter),
            $request->query->getInt('page', 1),
            10
        );
        $activationForm = [];
        foreach ($users as $user) {
            $activationForm[$user->getId()] = $this->createForm(ActivationType::class, $user, [
                'method' => "PUT",
                'action' => $this->generateUrl('activate_user', array('id' => $user->getId())),
                'validation_groups' => array('edit'),
            ])
                ->createView();
        }

        return [
            "users" => $users,
            "filterForm" => $filterForm->createView(),
            "activationForm" => $activationForm
        ];
    }

    /**
     * @Route("/user/activate/{id}", name="activate_user")
     * @Template("@App/add.html.twig")
     *
     * @Method("PUT")
     * @param  Request $request
     * @param  User $user
     * @return RedirectResponse
     */
    public function activationAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ActivationType::class, $user, [
            'method' => "PUT",
            'action' => $this->generateUrl('activate_user', array('id' => $user->getId())),
            'validation_groups' => array('edit'),
        ]);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl("users_list"));
    }

    /**
     * @Route("/user/add", name="add_user")
     * @Template("@App/add.html.twig")
     *
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user, [
            'action' => $this->generateUrl('add_user'),
            'validation_groups' => array('registration'),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl("users_list"));
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/user/edit/{id}", name="edit_user")
     * @Template("@App/add.html.twig")
     *
     * @param Request $request
     * @param User $user
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EditType::class, $user, [
            'action' => $this->generateUrl('edit_user', array('id' => $user->getId())),
            'method' => 'POST',
            'validation_groups' => array('edit'),
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl("users_list"));
            }
        }

        return ['form' => $form->createView()];
    }
}
