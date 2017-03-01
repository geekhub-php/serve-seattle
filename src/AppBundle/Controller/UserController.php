<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users_list")
     * @Template("@App/users.html.twig")
     *
     * @return array
     */
    public function usersListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->getUsersByParams($request->query);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($users, $request->query->getInt('page', 1), 10);
        return [
            "users" => $pagination
        ];
    }

    /**
     * @Route("/user/activate/{id}", name="activate_user")
     * @Template("@App/add.html.twig")
     *
     * @return RedirectResponse
     */
    public function userActivateAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        if($user->isEnabled()){
            $user->setStatus(false);
        } else {
            $user->setStatus(true);
        }
        $em->persist($user);
        $em->flush();

        return new RedirectResponse($this->generateUrl("users_list"));
    }

    /**
     * @Route("/user/add", name="add_user")
     * @Template("@App/add.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function userAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user, [
            'action' => $this->generateUrl('add_user'),
            'method' => 'POST',
            'validation_groups' => array('registration'),
        ])
            ->add('Save', SubmitType::class, array(
                'attr' => ['class' => 'btn pull-right btn-warning']
            ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            return new RedirectResponse($this->generateUrl("users_list"));
        }
        return ['form' => $form->createView()];
    }

    /**
     * @Route("/user/edit/{id}", name="edit_user")
     * @Template("@App/add.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function userEditAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\UserType', $user, [
            'action' => $this->generateUrl('edit_user', array('id' => $user->getId())),
            'method' => 'POST',
            'validation_groups' => array('edit'),
        ])
            ->add('Save', SubmitType::class, array(
                'attr' => ['class' => 'btn pull-right btn-warning']
            ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            return new RedirectResponse($this->generateUrl("users_list"));
        }
        return ['form' => $form->createView()];
    }
}