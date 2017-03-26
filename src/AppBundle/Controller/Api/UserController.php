<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\S3\Image;
use AppBundle\Entity\User;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
}
