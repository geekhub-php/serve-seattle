<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\DTO\DtoUser;
use AppBundle\Entity\DTO\DtoEvent;
use AppBundle\Entity\Event;
use AppBundle\Entity\FormRequest;
use AppBundle\Entity\Survey\Survey;
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
        if (!$user->isEnabled()) {
            throw new JsonHttpException(400, 'Account is not enabled.');
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
        $events = $this->getDoctrine()->getRepository(Event::class)->selectNotExpiredByUser($this->getUser(), true);
        $requests = $user->getFormRequests();
        $surveys = $user->getSurveys();
        $surveys = $surveys->matching(Criteria::create()->where(Criteria::expr()->eq('status', 'current')));
        $array = new ArrayCollection(
            array_merge($events, $requests->toArray(), $surveys->toArray())
        );
        $news = $array->matching(Criteria::create()->orderBy(array('updatedAt' => Criteria::DESC))->setFirstResult(0)
            ->setMaxResults(3));
        $sortNews = array_fill_keys(['events', 'surveys', 'requests'], []);
        $calendar = $this->get('app.google_calendar');
        foreach ($news as $new) {
            if ($new instanceof Event) {
                $sortNews['events'][] = new DtoEvent($calendar
                    ->getEventById($new->getGoogleId()));
            }
            if ($new instanceof Survey) {
                $sortNews['surveys'][] = $new;
            }
            if ($new instanceof FormRequest) {
                $sortNews['requests'][] = $new;
            }
        }
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

        return $this->json(
            ['news' => $sortNews, 'events' => $events, 'surveys' => $surveys]
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
        $surveys = $surveys->matching(Criteria::create()->where(Criteria::expr()->eq('status', 'current')));
        $array = new ArrayCollection(
            array_merge($events->toArray(), $requests->toArray(), $surveys->toArray())
        );
        $news = $array->matching(Criteria::create()->orderBy(array('updatedAt' => Criteria::DESC))->setFirstResult(0)
            ->setMaxResults(3));
        $calendar = $this->get('app.google_calendar');

        $sortNews = [];
        foreach ($news as $new) {
            if ($new instanceof Event) {
                $item = new DtoEvent($calendar
                    ->getEventById($new->getGoogleId()));
                $sortNews[] = [
                    'kind' => 'event',
                    'description' => $item->getSummary(),
                    'id' => $item->getGoogleEventId(),
                    'location' => $item->getLocation(),
                    'title' => $item->getSummary(),
                    'start' => $item->getStart()->format('c'),
                    'end' => $item->getEnd()->format('c'),
                ];

            }
            if ($new instanceof Survey) {
                $sortNews[] = [
                    'kind' => 'survey',
                    'id' => $new->getId(),
                    'type' => $new->getType()->getName(),
                    'status' => $new->getStatus(),
                ];
            }
            if ($new instanceof FormRequest) {
                $sortNews[] = [
                    'kind' => 'request',
                    'id' => $new->getId(),
                    'type' => $new->getType(),
                    'status' => $new->getStatus(),
                    'reason' => $new->getReason(),
                    'date' => $new->getDate(),
                    'createdAt' => $new->getCreatedAt(),
                    'updatedAt' => $new->getUpdatedAt(),
                ];
            }
        }

        return $this->json(
            ['news' => $sortNews]
        );
    }
}
