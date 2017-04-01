<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\S3\Image;
use AppBundle\Entity\User;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class UserController extends Controller
{
    /**
     * @Route("/avatar", name="api_avatar")
     * @Method({"PUT"})
     */
    public function avatarAction(Request $request)
    {
        $headers = $request->headers;
        /** @var User $user */
        $user = $this->getUser();

        $image = new Image(sprintf('user/%d/avatar', $user->getId()));
        $image
            ->setContentType($headers->get('Content-Type'))
            ->setContent($request->getContent());
        /** @var ConstraintViolationList $errors */
        $errors = $this->get('validator')->validate($image, null, ['Api']);

        if ($errors->count()) {
            $outErrors = [];

            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $outErrors['headers'][$error->getPropertyPath()] = $error->getMessage();
            }

            throw new JsonHttpException(400, 'Bad Request', $outErrors);
        }
        $user->setImage($image);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['user' => $user], 201, [], [AbstractNormalizer::GROUPS => ['Short']]);
    }

    /**
     * @Route("/user")
     * @Method({"GET"})
     */
    public function userAction()
    {
        return $this->json(['user' => $this->getUser()], 200, [], [AbstractNormalizer::GROUPS => ['Detail']]);
    }

    /**
     * @Route("/password_reset", name="password_reset")
     * @Method({"POST"})
     *
     * @return JsonResponse
     */
    public function resetPasswordAction(Request $request)
    {
        $data = $request->getContent();
        $serializer = $this->get('serializer');
        $data = $serializer->decode($data, 'json');
        if (!isset($data['email']) || $data['email'] == null) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $user = $this->getDoctrine()->getRepository(User::class)->loadUserByEmail($data['email']);
        if (!$user) {
            throw new JsonHttpException(404, 'There is no user with this email');
        }
        $token = $user->getApiToken();
        if ($token == null) {
            $token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
            $user->setApiToken($token);
        }
        $tomorrow = (new \DateTime())->modify('+24 hours');
        $user->setLinkExpiredAt($tomorrow);
        $this->getDoctrine()->getManager()->flush();
        $url = $this->generateUrl('password_update', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom($this->getParameter('mailer_from'))
            ->setTo($data['email'])
            ->setBody($this->renderView('AppBundle:Email:reset_password.html.twig', [
                'user' => $user, 'url' => $url, ]), 'text/html');
        $this->get('mailer')->send($message);

        return $this->json(['message' => "You've got an update link on you email. Check your email"], 201);
    }

    /**
     * @param Request $request, string $token
     * @Route("/password_update/{token}", name="password_update")
     * @Method({"GET", "POST"})
     *
     * @return JsonResponse
     */
    public function updatePasswordAction(Request $request, $token)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->loadUserByToken($token);
        if ((!$user)) {
            return $this->render('AppBundle:User:update_password.html.twig', array(
                'message' => 'Your link is expired!',
            ));
        }
        $linkDate = $user->getLinkExpiredAt();
        $date = new \DateTime('now');
        if (($linkDate < $date)) {
            return $this->render('AppBundle:User:update_password.html.twig', array(
                'message' => 'Your link is expired!',
            ));
        }
        $form = $this->createForm(\AppBundle\Form\User\ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->render('AppBundle:User:update_password.html.twig', array(
                'message' => 'Your password was successfully updated!', 'user' => $user,
            ));
        }

        return $this->render('AppBundle:User:update_password.html.twig', array(
            'message' => 'Please, enter your new password', 'form' => $form->createView(),
        ));
    }
}
