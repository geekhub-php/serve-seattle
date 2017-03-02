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
     * @param Request $request
     * @return array
     */
    public function usersListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $users = $paginator->paginate($em->getRepository(User::class)->selectUsersByParams($request->query), $request->query->getInt('page', 1), 10);
        $activateForm = [];
        foreach ($users as $user) {
            $activateForm[$user->getId()] = $this->createActivateUserForm($user)->createView();
        }
        return [
            "users" => $users,
            'activateForm' => $activateForm
        ];
    }

    /**
     * @Route("/user/activate/{id}", name="activate_user")
     * @Template("@App/add.html.twig")
     *
     * @param  User $user
     * @return RedirectResponse
     */
    public function userActivateAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setStatus($user->isEnabled() ? false : true);
        $em->persist($user);
        $em->flush();
        return $this->redirect($this->generateUrl("users_list"));
    }

    /**
     * Creates a form to activate|deactivate User profile.
     * @param User $user
     * @return \Symfony\Component\Form\Form The form
     */
    private function createActivateUserForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('activate_user', array('id' => $user->getId())))
            ->setMethod('PUT')
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * @Route("/user/add", name="add_user")
     * @Template("@App/add.html.twig")
     *
     * @param Request $request
     * @return array|RedirectResponse
     */
    public function userAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user, [
            'action' => $this->generateUrl('add_user'),
            'validation_groups' => array('registration'),
        ])
            ->add('Register', SubmitType::class, array(
                'attr' => ['class' => 'btn pull-right btn-warning']
            ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl("users_list"));
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

            return $this->redirect($this->generateUrl("users_list"));
        }
        return ['form' => $form->createView()];
    }
}
