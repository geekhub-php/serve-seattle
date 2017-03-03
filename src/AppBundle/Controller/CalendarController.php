<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends Controller
{
    /**
     * @Route("/schedule/events")
     * @return JsonResponse
     */
    public function eventsListAction()
    {
        $result = $this->get('app.google_calendar')->getEventList();

        dump($result);die;

        return new JsonResponse($result);
    }

    /**
     * @Route("/schedule/event/new")
     * @return JsonResponse
     */
    public function newEventAction()
    {
        $result = $this->get('app.google_calendar')->newEvent();

        return new JsonResponse($result);
    }

    /**
     * @Route("/schedule/event")
     */
    public function singleEventAction()
    {
        $event = $this->get('app.google_calendar')
            ->getEventById();
        dump($event);die;
    }

}
