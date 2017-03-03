<?php

namespace AppBundle\Services;

class GoogleCalendarManager
{
    private $client;

    private $calendar;

    public function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setApplicationName('seattle');
        $this->client->setScopes([\Google_Service_Calendar::CALENDAR]);
        $this->client->setAuthConfig(__DIR__.'/../../../app/config/credentials/credentials.json');

        $this->calendar = new \Google_Service_Calendar($this->client);

        $scope = new \Google_Service_Calendar_AclRuleScope();
        $scope->setType('public');

        $rule = new \Google_Service_Calendar_AclRule();
        $rule->setRole('writer');
        $rule->setScope($scope);

        $this->calendar->acl->insert('primary', $rule);
    }

    public function newEvent()
    {
        $event = new \Google_Service_Calendar_Event();

        $event->setSummary('TITLE');
        $event->setDescription('DESCRIPTION');
        $event->setLocation('God know where');
        $event->setVisibility('public');

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDateTime('2017-03-10T09:00:00-07:00');
        $event->setStart($start);

        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDateTime('2017-03-11T09:00:00-07:00');
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
}
