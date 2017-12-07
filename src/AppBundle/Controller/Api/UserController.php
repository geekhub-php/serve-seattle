<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\S3\Image;
use AppBundle\Entity\User;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        if (!$data) {
            throw new JsonHttpException(400, 'Bad Request.');
        }
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
        $title = 'Hello '.$user->getFirstName();
        $this->get('app.email_notification')->sendNotification($user->getEmail(), $title, 'reset', $user);

        return $this->json(['message' => "You've got an update link on you email. Check your email"], 201);
    }

    /**
     * @Route("/email")
     * @Method({"PUT"})
     */
    public function emailAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->get('serializer')
            ->deserialize($request->getContent(), User::class, 'json', [
                AbstractNormalizer::OBJECT_TO_POPULATE => $user
            ]);
        $errors = $this->get('validator')->validate($user, null, ['edit']);
        if ($errors->count()) {
            $outErrors = [];

            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $outErrors[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new JsonHttpException(400, 'This value is already used.', $outErrors);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['user' => $this->getUser()], 200, [], [AbstractNormalizer::GROUPS => ['Short']]);
    }
}
