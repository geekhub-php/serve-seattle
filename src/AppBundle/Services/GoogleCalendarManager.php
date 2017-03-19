<?php

namespace AppBundle\Services;

use AppBundle\Entity\DTO\DtoEvent;

class GoogleCalendarManager
{
    private $calendar;

    public function __construct(GoogleClientFactory $factory)
    {
        $this->calendar = $factory->createCalendar('user', 'owner');
    }

    public function createEvent(DtoEvent $dtoEvent, $data = [])
    {
        $event = new \Google_Service_Calendar_Event();
        $event->setSummary($dtoEvent->getSummary());
        $event->setDescription($dtoEvent->getDescription());
        $event->setLocation($dtoEvent->getLocation());
        $event->setVisibility('public');
        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDateTime($dtoEvent->getStart());
        $event->setStart($start);
        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDateTime($dtoEvent->getEnd());
        $event->setEnd($end);

        return $this->calendar->events->insert('primary', $event, $data);
    }

    public function getEventList($query = [])
    {
        $events = $this->calendar
            ->events
            ->listEvents('primary', $query);

        return [
            'nextPageToken' => $events->getNextPageToken(),
            'events' => $events->getItems(),
        ];
    }

    public function getEventById($id)
    {
        $event = $this->calendar->events->get('primary', $id);
        if ($event->status == 'cancelled') {
            return;
        }

        return $event;
    }

    public function deleteEvent($id)
    {
        return $this->calendar->events->delete('primary', $id);
    }

    public function editEvent(DtoEvent $dtoEvent, $id, $data = [])
    {
        $event = $this->getEventById($id);
        $event->setSummary($dtoEvent->getSummary());
        $event->setDescription($dtoEvent->getDescription());
        $event->setLocation($dtoEvent->getLocation());
        $event->setVisibility('public');

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDateTime($dtoEvent->getStart());
        $event->setStart($start);

        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDateTime($dtoEvent->getEnd());
        $event->setEnd($end);

        return $this->calendar->events->patch('primary', $id, $event, $data);
    }

    public function clear()
    {
        $events = $this->calendar
            ->events
            ->listEvents('primary')
            ->getItems();

        foreach ($events as $event) {
            $this->deleteEvent($event->getId());
        }
    }
}
