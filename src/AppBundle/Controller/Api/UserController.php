<?php

namespace AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserController extends Controller
{
    /**
     * @Route("/users")
     *
     * @Method("GET")
     * @return JsonResponse
     */
    public function listAction()
    {
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();
        if (!$users) {
            throw new NotFoundHttpException();
        }
        return $this->json(['users' => $users], 200, [], [AbstractNormalizer::GROUPS => ['Short']]);
    }
}