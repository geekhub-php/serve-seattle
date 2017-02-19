<?php


namespace AppBundle\Controller\Api;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @Route("/login", name="api_login")
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:UserIntern')
            ->findOneBy(['userName' => $request->get('login')]);
        if (!$user){
            return new JsonResponse(['message' => 'Bad credentials'], 403);
        }
        $result = $this->get('security.encoder_factory')
            ->getEncoder($user)
            ->isPasswordValid($user->getPassword(), $request->get('password'), null);
        if (!$result){
            return new JsonResponse(['message' => 'Bad credentials'], 403);
        }

        $token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        //set token to user

        return new JsonResponse(['X-AUTH-TOKEN' => $token]);
    }
}