<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DTO\DtoEvent;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use AppBundle\Form\EventType;
use Mcfedr\JsonFormBundle\Controller\JsonController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends JsonController
{
    /**
     * @Route("/schedule/events")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function eventsListAction(Request $request)
    {
        $result = $this->get('app.google_calendar')
            ->getEventList('primary', $request->query->all());

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @Route("/schedule/event/new")
     * @Method("POST")
     *
     * @return JsonResponse
     */
    public function newEventAction(Request $request)
    {
        $dtoEvent = new DtoEvent();
        $form = $this->createForm(EventType::class, $dtoEvent);
        $this->handleJsonForm($form, $request);
        $result = $this->get('app.google_calendar')
            ->createEvent($dtoEvent);
        if (!$result) {
            return $this->json(['error' => 'Event has not been created'], 412);
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')
            ->find($dtoEvent->getUser());
        $event = new Event();
        $event->setGoogleId($result->id);
        $event->setUsers($user);
        $user->setEvent($event);

        $em->persist($user);
        $em->persist($event);
        $em->flush();

        return $this->json(['success' => 'Event Created']);
    }

    /**
     * @param $id
     * @Route("/schedule/event/{id}")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function singleEventAction($id)
    {
        /** @var Event $event */
        $event = $this->getDoctrine()->getRepository('AppBundle:Event')
            ->findByGoogleId($id);
        $user = $event->getUsers()->first();
        $googleEvent = $this->get('app.google_calendar')
            ->getEventById($id);
        if (!$googleEvent) {
            return $this->json(['error' => 'Event not found'], 404);
        }
        $serializer = $this->get('serializer');
        $user = $serializer->normalize(
            $user,
            null,
            ['groups' => ['Short']]
        );
        return new JsonResponse(['user' => $user, 'event' => $googleEvent]);
    }

    /**
     * @Route("/api/schedule/event/user")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function currentUserEventsAction()
    {
        $user = $this->getUser();

        $events = $user->getEvents();

        $googleEvents = [];

        $calendar = $this->get('app.google_calendar');
        foreach ($events as $event) {
            $googleEvents[] = $calendar
                ->getEventById($event->getGoogleId());
        }
        if (!$googleEvents) {
            return $this->json(['error' => 'Events not found'], 404);
        }

        $response = ['events' => $googleEvents];

        return new JsonResponse($response);
    }

    /**
     * @Route("/schedule/event/user/{id}")
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function userEventsAction(User $user)
    {
        $events = $user->getEvents();

        $googleEvents = [];

        $calendar = $this->get('app.google_calendar');
        foreach ($events as $event) {
            $googleEvents[] = $calendar
                ->getEventById($event->getGoogleId());
        }
        if (!$googleEvents) {
            return $this->json(['error' => 'Events not found'], 404);
        }

        $user = $this->get('serializer')->normalize($user, null, ['groups' => ['Short']]);

        $response = ['user' => $user, 'events' => $googleEvents];

        return new JsonResponse($response);
    }

    /**
     * @param $id
     * @Route("/schedule/event/{id}")
     * @Method("DELETE")
     * @return JsonResponse
     */
    public function removeEventAction($id)
    {
        $result = $this->get('app.google_calendar')
            ->deleteEvent($id);

        return $this->json($result);
    }

    /**
     * @param Request $request
     * @Method("PATCH")
     * @Route("/schedule/event/{id}")
     *
     * @return JsonResponse
     */
    public function editEventAction(Request $request, $id)
    {
        $dtoEvent = new DtoEvent();
        $form = $this->createForm(EventType::class, $dtoEvent);
        $this->handleJsonForm($form, $request);
        $this->get('app.google_calendar')
            ->editEvent($dtoEvent, $id);

        return $this->json(['success' => 'Event edited']);
    }

    /**
     * FOR DEV ONLY.
     *
     * @Route("/schedule/clear")
     */
    public function clearAction()
    {
        $result = $this->get('app.google_calendar')->clear();

        return $this->json($result);
    }
}
