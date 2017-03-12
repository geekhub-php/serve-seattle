<?php

namespace AppBundle\Controller;

//use AppBundle\Entity\Request;
use AppBundle\Entity\UserIntern;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("@App/dashboard.html.twig")
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/login", name="login")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @Method({"GET", "POST"})
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            '@App/login.html.twig', array(
            'last_username' => $lastUsername, //$lastUsername,
            'error' => $error,
            )
        );
    }

    /**
     * @Route("/users", name="user_list")
     * @Template("@App/users.html.twig")
     *
     * @return array
     */
    public function usersListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(UserIntern::class)->findall();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($users, $request->query->getInt('page', 1), 10);

        return [
            'users' => $pagination,
        ];
    }

    /**
     * @Route("/user/add", name="add_user")
     * @Template("@App/add.html.twig")
     *
     * @return array
     */
    public function usersAddAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(
            'AppBundle\Form\UserType', $user, [
            'action' => $this->generateUrl('add_user'),
            'method' => 'POST',
            ]
        )
            ->add(
                'Save', SubmitType::class, array(
                'attr' => ['class' => 'btn pull-right btn-warning'],
                )
            );
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }
}
