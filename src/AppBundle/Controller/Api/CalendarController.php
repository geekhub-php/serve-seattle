<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\DTO\DtoEvent;
use AppBundle\Entity\Event;
use AppBundle\Exception\JsonHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends Controller
{
    /**
     * @Route("/events")
     * @Method({"GET"})
     */
    public function userEventsAction()
    {
        $events = $this->getDoctrine()->getRepository(Event::class)
            ->selectNotExpiredByUser($this->getUser());
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

        return new JsonResponse(['events' => $events]);
    }

    /**
     * @Route("/events/{id}")
     * @Method("GET")
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

        return new JsonResponse(['event' => $event]);
    }
}
