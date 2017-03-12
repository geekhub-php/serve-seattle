<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Form\LoginType;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends JsonController
{
    /**

     * @param Request $request
     * @Route("/login", name="api_login")
     * @Method("POST")
     *
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $userCredentials = new User();

        $form = $this->createForm(LoginType::class, $userCredentials);

        $this->handleJsonForm($form, $request);

        $user = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findOneBy(['email' => $userCredentials->getEmail()]);

        if (!$user) {
            return $this->json(['message' => 'Bad credentials'], 401);
        }

        $result = $this->get('security.encoder_factory')
            ->getEncoder($user)
            ->isPasswordValid($user->getPassword(), $userCredentials->getPassword(), null);
        if (!$result) {
            return $this->json(['message' => 'Bad credentials'], 401);
        }

        $token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $em = $this->getDoctrine()
            ->getManager();
        $user->setApiToken($token);

        $em->persist($user);

        $em->flush();

        $serializer = $this->get('serializer');
        $json = $serializer->normalize(
            $user,
            null,
            array('groups' => array('Detail'))
        );

        return $this->json(
            ['user' => $json, 'X-AUTH-TOKEN' => $token]
        );
    }

    /**
     * @Route("/user", name="user")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function securityTestAction()
    {
        return $this->json(['autorization' => 'works!']);
    }
}
