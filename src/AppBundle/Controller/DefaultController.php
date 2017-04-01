<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

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

    /**
     * @param Request $request, string $token
     * @Route("/password_update/{token}", name="password_update")
     * @Template("@App/User/update_password.html.twig")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function updatePasswordAction(Request $request, $token)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->loadUserByToken($token);
        if ((!$user)) {
            return ['message' => 'Your link is expired!'];
        }
        $linkDate = $user->getLinkExpiredAt();
        $date = new \DateTime('now');
        if (($linkDate < $date)) {
            return ['message' => 'Your link is expired!'];
        }
        $form = $this->createForm(\AppBundle\Form\User\ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $encoder = $this->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($user, $user->getPlainPassword()));
            $em->persist($user);
            $em->flush();

            return ['message' => 'Your password was successfully updated!', 'user' => $user];
        }

        return ['message' => 'Please, enter your new password', 'form' => $form->createView()];
    }
}
