<?php


namespace AppBundle\Services;


class GoogleCalendarManager
{
    /* @var \Google_Client */
    private $client;

    /* @var \Google_Service_Calendar */
    private $calendar;

    public function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setApplicationName('serve-seattle');
        $this->client->setScopes([\Google_Service_Calendar::CALENDAR]);
        $this->client->setAuthConfig(__DIR__.'/../../../cred.json');

        $this->calendar = new \Google_Service_Calendar($this->client);
//
//        $role =  new \Google_Service_Calendar_AclRule;
//        $scope = new \Google_Service_Calendar_AclRuleScope();
//        $scope->setType('default');
//        $role->setRole('reader');
//        $role->setScope($scope);
//
//        $this->calendar->acl->insert('primary', $role);
    }

    public function newEvent($calendarId = 'primary')
    {
        $event = new \Google_Service_Calendar_Event([
            'summary' => 'title',
            'description' => 'descsadasdasd',
            'start' => ['dateTime' => "2017-03-4T09:00:00-07:00"],
            'end' => ['dateTime' => "2017-03-4T11:00:00-07:00"]
        ]);
        $event->setLocation('God know where');
        $event->setVisibility('public');

        return $this->calendar->events->insert($calendarId, $event);
    }

    public function getEventList($calendarId = 'primary')
    {
       return $this->calendar
           ->events
           ->listEvents($calendarId)
           ->getItems();
    }

    public function getEventById()
    {
        return $this->calendar->events->get('primary', 'q5tsnq70tbnur8cmlidrpcvvc0');
    }
//
//    public function newCalendar()
//    {
//        $calendar = new \Google_Service_Calendar_Calendar();
//        $calendar->setSummary('calendarSummary');
//        $calendar->setTimeZone('America/Los_Angeles');;
//
//        $createdCalendar = $this->calendar->calendars->insert($calendar);
//
//        return $createdCalendar;
//    }

}