<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @return array
     * @Method({"GET", "POST"})
     * @Template("@App/login.html.twig")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername, //$lastUsername,
            'error' => $error,
        ];
    }
}
