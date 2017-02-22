<?php

namespace AppBundle\Controller;

//use AppBundle\Entity\Request;
use AppBundle\Entity\UserIntern;
use phpDocumentor\Reflection\Types\Array_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        return $this->render('@App/login.html.twig', array(
            'last_username' => $lastUsername, //$lastUsername,
            'error' => $error,
        ));
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
            "users" => $pagination
        ];
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }
}
