<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\DTO\DtoUser;
use AppBundle\Exception\JsonHttpException;
use AppBundle\Form\LoginType;
use Aws\AwsClient;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
        $userCredentials = new DtoUser();

        $form = $this->createForm(LoginType::class, $userCredentials);

        $this->handleJsonForm($form, $request);

        $user = $this->getDoctrine()->getRepository('AppBundle:User')
            ->findOneBy(['email' => $userCredentials->getEmail()]);

        if (!$user) {
            throw new JsonHttpException(400, 'Bad credentials');
        }

        $result = $this->get('security.encoder_factory')
            ->getEncoder($user)
            ->isPasswordValid($user->getPassword(), $userCredentials->getPassword(), null);
        if (!$result) {
            throw new JsonHttpException(400, 'Bad credentials');
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
            array('groups' => array('Short'))
        );

        return $this->json(
            ['user' => $json, 'X-AUTH-TOKEN' => $token]
        );
    }

    /**
     * @Route("/user")
     * @Method({"GET"})
     */
    public function userAction()
    {
        $this->get('app.email_notification')->sendNotification('bloodboil@list.ru');

        return $this->json(['user' => $this->getUser()], 200, [], [AbstractNormalizer::GROUPS => ['Detail']]);
    }
}
