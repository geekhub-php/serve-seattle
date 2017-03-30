<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\DTO\DtoUser;
use AppBundle\Entity\DTO\DtoEvent;
use AppBundle\Entity\Event;
use AppBundle\Exception\JsonHttpException;
use AppBundle\Form\LoginType;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @Route("/dashboard")
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function dashboardAction()
    {
        $user = $this->getUser();
        $events = $this->getDoctrine()->getRepository(Event::class)
            ->selectNotExpiredByUser($this->getUser());
        $requests = $user->getFormRequests();
        $surveys = $user->getSurveys();
        $array = new ArrayCollection(
            array_merge($events->toArray(), $requests->toArray(), $surveys->toArray())
        );
        $news = $array->matching(Criteria::create()->orderBy(array('updatedAt' => Criteria::DESC))->setFirstResult(0)
            ->setMaxResults(3));
        $events = $events->matching(Criteria::create()->setFirstResult(0)->setMaxResults(2));
        $calendar = $this->get('app.google_calendar');
        $googleEvents = [];
        foreach ($events as $event) {
            $googleEvents[] = $calendar
                ->getEventById($event->getGoogleId());
        }
        $events = [];
        foreach ($googleEvents as $event) {
            if ($event) {
                $events[] = new DtoEvent($event);
            }
        }
        $surveys = $surveys->matching(Criteria::create()->where(Criteria::expr()->eq('status', 'current')));

        return $this->json(
            ['news' => $news, 'events' => $events, 'surveys' => $surveys]
        );
    }

    /**
     * @Route("/news")
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function newsAction()
    {
        $user = $this->getUser();
        $events = $user->getEvents();
        $requests = $user->getFormRequests();
        $surveys = $user->getSurveys();
        $array = new ArrayCollection(
            array_merge($events->toArray(), $requests->toArray(), $surveys->toArray())
        );
        $news = $array->matching(Criteria::create()->orderBy(array('updatedAt' => Criteria::DESC))->setFirstResult(0)
            ->setMaxResults(100));

        return $this->json(
            ['news' => $news]
        );
    }
}
