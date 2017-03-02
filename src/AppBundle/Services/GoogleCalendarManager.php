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
        $this->client->setApplicationName('seatleClient');
        $this->client->setScopes([\Google_Service_Calendar::CALENDAR]);
        $this->client->setAuthConfig('/home/ed/work/hw2/serve-seattle/cred.json');

        $this->calendar = new \Google_Service_Calendar($this->client);
    }

    public function newEvent()
    {
        $event = new \Google_Service_Calendar_Event(array(
            'location' => '800 Howard St., San Francisco, CA 94103',
            'description' => 'RRRRRRRRRRRRRRRAAAAAAAAAAAAAAAAAAWWWWWW!!!!!!!!!!!',
            'start' => array(
                'dateTime' => '2017-03-3T09:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ),
            'end' => array(
                'dateTime' => '2017-03-4T17:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            )
        ));

        $calendarId = 'primary';
        return $this->calendar->events->insert($calendarId, $event);
    }

    public function getEvent()
    {
       return $this->calendar->events->get('primary', "025qm93a7pcetsua35vm29qbn4");
    }


}