<?php

namespace AppBundle\Services;

use AppBundle\Entity\DTO\DtoEvent;

class GoogleCalendarManager implements GoogleCalendarInterface
{
    private $calendar;

    public function __construct(GoogleClientFactory $factory)
    {
        $this->calendar = $factory->createCalendar('default', 'reader');
    }

    public function createEvent($dtoEvent)
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

        return $this->calendar->events->insert('primary', $event);
    }

    public function getEventList($calendarId = 'primary')
    {
        return $this->calendar
            ->events
            ->listEvents($calendarId)
            ->getItems();
    }

    public function getEventById($id)
    {
        return $this->calendar->events->get('primary', $id);
    }

    public function deleteEvent($id)
    {
        return $this->calendar->events->delete('primary', $id);
    }

    public function editEvent($id, $data)
    {
        $event = $this->getEventById($id);
        $event->setSummary($data['title']);
        $event->setDescription($data['description']);
        $event->setLocation($data['location']);
        $event->setVisibility('public');

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDateTime($data['start']);
        $event->setStart($start);

        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDateTime($data['end']);
        $event->setEnd($end);

        return $this->calendar->events->patch('primary', $id, $event);
    }

    public function clear()
    {
        $events = $this->getEventList();

        foreach ($events as $event) {
            $this->deleteEvent($event->getId());
        }
    }
}
