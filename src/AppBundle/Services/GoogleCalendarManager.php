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
        $scope->setType('default');

        $rule = new \Google_Service_Calendar_AclRule;
        $rule->setRole('reader');
        $rule->setScope($scope);

        $this->calendar->acl->insert('primary', $rule);
    }

    public function newEvent()
    {
        $event = new \Google_Service_Calendar_Event([
            'summary' => 'title',
            'description' => 'descsadasdasd',
            'start' => ['dateTime' => "2017-03-5T09:00:00-07:00"],
            'end' => ['dateTime' => "2017-03-6T11:00:00-07:00"],
            'location' => 'God know where'
        ]);


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

}