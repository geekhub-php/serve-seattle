<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\DtoEvent;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use AppBundle\Exception\JsonHttpException;
use AppBundle\Form\EventType;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/schedule/events")
 */
class CalendarController extends JsonController
{
    /**
     * @Route("/")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function eventsListAction(Request $request)
    {
        $googleEvents = $this->get('app.google_calendar')
            ->getEventList($request->query->all());
        $events = [];
        foreach ($googleEvents['events'] as $event) {
            $events[] = new DtoEvent($event);
        }

        return $this->json(['pageToken' => $googleEvents['pageToken'], 'events' => $events]);
    }

    /**
     * @param Request $request
     * @Route("")
     * @Method("POST")
     *
     * @return JsonResponse
     */
    public function newEventAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (!$data['event']['start'] || !$data['event']['end'] || !$data['event']['user']) {
            throw new JsonHttpException(400, 'Bad request.');
        }

        $dtoEvent = new DtoEvent();
        $form = $this->createForm(EventType::class, $dtoEvent);
        $this->handleJsonForm($form, $request);
        $result = $this->get('app.google_calendar')
            ->createEvent($dtoEvent, $request->query->all());
        if (!$result) {
            throw new JsonHttpException(412, 'Event has not been created');
        }
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository('AppBundle:User')
            ->find($dtoEvent->getUser());

        if (!$user) {
            throw new JsonHttpException(404, 'User not found.');
        }
        $event = new Event();
        $event->setGoogleId($result->id);
        $event->setUser($user);
        $event->setExpiredAt(new \DateTime($result->getEnd()->dateTime));
        $user->setEvent($event);

        $em->persist($user);
        $em->flush();
        $event = new DtoEvent($result);

        return $this->json(['event' => $event]);
    }

    /**
     * @param $id
     * @Route("/{id}")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function singleEventAction($id)
    {
        /** @var Event $event */
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')
            ->findByGoogleId($id);
        $user = $event->getUser();
        if (!$user) {
            throw new JsonHttpException(404, 'User not found.');
        }
        $googleEvent = $this->get('app.google_calendar')
            ->getEventById($id);
        $event = new DtoEvent($googleEvent);
        $user = $this->get('serializer')->normalize($user, null, ['groups' => ['Short']]);

        return new JsonResponse(['user' => $user, 'event' => $event]);
    }

    /**
     * @Route("/user/{id}")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function userEventsAction($id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $events = $this->getDoctrine()->getRepository(Event::class)
        ->selectNotExpiredByUser($user);
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
        $user = $this->get('serializer')->normalize($user, null, ['groups' => ['Short']]);

        return new JsonResponse(['user' => $user, 'events' => $events]);
    }

    /**
     * @param $id
     * @Route("/{id}")
     * @Method("DELETE")
     *
     * @return JsonResponse
     */
    public function removeEventAction($id)
    {
        $this->get('app.google_calendar')
            ->deleteEvent($id);
        $em = $this->getDoctrine()->getManager();
        /** @var Event $event */
        $event = $em->getRepository('AppBundle:Event')->findByGoogleId($id);
        $em->remove($event);
        $em->flush();

        return $this->json(['success' => 'Event was removed']);
    }

    /**
     * @param Request $request
     * @Method("PATCH")
     * @Route("/{id}")
     *
     * @return JsonResponse
     */
    public function editEventAction(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);

        if (!$data['event']['start'] || !$data['event']['end'] || !$data['event']['user']) {
            throw new JsonHttpException(400, 'Bad request.');
        }

        $dtoEvent = new DtoEvent();
        $form = $this->createForm(EventType::class, $dtoEvent);
        $this->handleJsonForm($form, $request);
        $result = $this->get('app.google_calendar')
            ->editEvent($dtoEvent, $id, $request->query->all());
        $event = new DtoEvent($result);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($dtoEvent->getUser());
        /** @var Event $ev */
        $ev = $this->getDoctrine()->getRepository(Event::class)->findByGoogleId($id);
        $ev->setUser($user);
        $em->flush();
        return $this->json(['event' => $event]);
    }

    /**
     * FOR DEV ONLY.
     *
     * @Method("PUT")
     * @Route("/clear")
     */
    public function clearAction()
    {
        $result = $this->get('app.google_calendar')->clear();

        return $this->json($result);
    }
}
