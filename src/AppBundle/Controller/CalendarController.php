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
     * @Route("/", name="events-list", options={"expose"=true})
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
     * @Route("", name="new-event", options={"expose"=true})
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
        $users = [];
        $em = $this->getDoctrine()->getManager();
        foreach ($dtoEvent->getUser() as $user => $id) {
            $user = $em->getRepository('AppBundle:User')
                ->find($id);
            if (!$user) {
                throw new JsonHttpException(404, "User with id $id not found.");
            }
            $users[] = $user;
        }
        $result = $this->get('app.google_calendar')
            ->createEvent($dtoEvent, $request->query->all());
        if (!$result) {
            throw new JsonHttpException(412, 'Event has not been created');
        }

        $event = new Event();
        $event->setGoogleId($result->id);
        foreach ($users as $user) {
            $event->addUser($user);
        }
        $event->setExpiredAt(new \DateTime($result->getEnd()->dateTime));

        $em->persist($event);
        $em->flush();

        $event = new DtoEvent($result);

        return $this->json(['event' => $event]);
    }

    /**
     * @param $id
     * @Route("/{id}", name="single-event", options={"expose"=true})
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
     * @Route("/user/{id}", name="user-events", options={"expose"=true})
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
     * @Route("/{id}", name="remove-event", options={"expose"=true})
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
     * @Route("/{id}", name="edit-event", options={"expose"=true})
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
        foreach ($data['event']['user'] as $userId) {
            $user = $this->getDoctrine()->getRepository('AppBundle:User')
                ->find($userId);
            if (!$user) {
                throw new JsonHttpException(404, "User with id $userId not found.");
            }
        }
        $form = $this->createForm(EventType::class, $dtoEvent);
        $this->handleJsonForm($form, $request);
        $result = $this->get('app.google_calendar')
            ->editEvent($dtoEvent, $id, $request->query->all());
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['event' => new DtoEvent($result)]);
    }
}
