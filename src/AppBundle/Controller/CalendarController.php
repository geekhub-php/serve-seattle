<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends Controller
{
    /**
     * @Route("/schedule/events")
     * @Method("GET")
     * @return JsonResponse
     */
    public function eventsListAction()
    {
        $result = $this->get('app.google_calendar')
            ->getEventList();

        return new JsonResponse($result);
    }

    /**
     * @param User $user
     * @Route("/schedule/event/new/{id}")
     * @Method({"GET", "POST"})
     * @return JsonResponse
     */
    public function newEventAction(User $user)
    {
        $result = $this->get('app.google_calendar')
            ->newEvent();
        if (!$result) {
            return $this->json([
                'error' => 'Event has not been created',
            ],
                412);
        }
        $em = $this->getDoctrine()->getManager();
        $event = new Event();
        $event->setGoogleId($result->id);
        $event->setUsers($user);
        $user->setEvent($event);

        $em->persist($user);
        $em->persist($event);
        $em->flush();

        return $this->json([
            'success' => 'Event created',
        ], 201);
    }

    /**
     * @param $id
     * @Route("/schedule/event/{id}")
     * @Method("GET")
     * @return JsonResponse
     */
    public function singleEventAction($id)
    {
        $event = $this->get('app.google_calendar')
            ->getEventById($id);
        if (!$event) {
            return $this->json([
                'error' => 'Event not found',
            ],
                404);
        }

        return $this->json($event, 200);
    }

    /**
     * @param User $user
     * @Route("/schedule/event/user/{id}")
     * @Method("GET")
     * @return JsonResponse
     */
    public function usersEventAction(User $user)
    {
        $events = $user->getEvents();

        $googleEvents = [];

        $calendar = $this->get('app.google_calendar');
        foreach ($events as $event) {
            $googleEvents[] = $calendar
                ->getEventById($event->getGoogleId());
        }
        if (!$googleEvents) {
            return $this->json([
                'error' => 'Events not found',
            ],
                404);
        }

        return $this->json($googleEvents);
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
     * @param $id
     * @Route("/schedule/event/{id}")
     * @Method("PATCH")
     * @return JsonResponse
     */
    public function editEventAction($id)
    {
        $result = $this->get('app.google_calendar')
            ->editEvent($id);
        return $this->json($result);
    }
}
