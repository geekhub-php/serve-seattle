<?php

namespace AppBundle\Services;

interface GoogleCalendarInterface
{
    /**
     * @param $data
     * Create a new Google Calendar Event
     * Returns the event representation
     */
    public function createEvent($data);

    /**
     * Returns the collection of events.
     */
    public function getEventList();

    /**
     * @param $id
     * Returns the event representation by Google Calendar id
     */
    public function getEventById($id);

    /**
     * @param $id
     * Removes the Event found by id.
     * Returns message and status code
     */
    public function deleteEvent($id);

    /**
     * @param $id
     * Edit the Event found by id.
     * Returns message and status code
     */
    public function editEvent($id);
}
