<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**

     * @param Request $request
     * @Route("/login", name="api_login")
     *
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return $this->json(['error' => 'Bad credentials'], 403);
        }

        $result = $this->get('security.encoder_factory')
            ->getEncoder($user)
            ->isPasswordValid($user->getPassword(), $data['password'], null);
        if (!$result) {
            return $this->json(['error' => 'Bad credentials'], 403);
        }

        $token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $em = $this->getDoctrine()
            ->getManager();
        $user->setApiToken($token);

        $em->persist($user);

        $em->flush();

        return $this->json(['X-AUTH-TOKEN' => $token]);
    }
}
