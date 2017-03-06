<?php

namespace AppBundle\Services;

class GoogleCalendarManager implements GoogleCalendarInterface
{
    private $calendar;

    public function __construct(GoogleClientFactory $factory)
    {
        $this->calendar = $factory->createCalendar();
    }

    public function createEvent($data = null)
    {
        $event = new \Google_Service_Calendar_Event();

        $event->setSummary('TITLE');
        $event->setDescription('DESCRIPTION');
        $event->setLocation('God know where');
        $event->setVisibility('public');

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDateTime('2017-03-07T09:00:00-07:00');
        $event->setStart($start);

        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDateTime('2017-03-07T09:00:00-07:00');
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

    public function editEvent($id)
    {
        $event = $this->getEventById($id);
        //do something
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
