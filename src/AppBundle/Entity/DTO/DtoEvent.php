<?php

namespace AppBundle\Entity\DTO;

use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class DtoEvent implements \JsonSerializable
{
    /**
     * @var User
     * @var Assert\NotBlank
     */
    private $user;

    private $summary;

    private $description;

    private $location;

    /**
     * @var Assert\NotBlank
     */
    private $start;

    /**
     * @var Assert\NotBlank
     */
    private $end;

    private $googleEventId;

    public function __construct(\Google_Service_Calendar_Event $event = null)
    {
        if ($event && $event instanceof \Google_Service_Calendar_Event) {
            $this->summary = $event->getSummary();
            $this->description = $event->getDescription();
            $this->summary = $event->getSummary();
            $this->location = $event->getLocation();
            $this->start = $event->getStart();
            $this->end = $event->getEnd();
            $this->googleEventId = $event->getId();
        }
    }

    public function jsonSerialize()
    {
        $description = json_decode($this->description, true);

        return [
            'user' => (int) $description['user'],
            'title' => $this->summary,
            'description' => $description['description'],
            'location' => $this->location,
            'start' => $this->start->dateTime,
            'end' => $this->end->dateTime,
            'id' => $this->googleEventId,
        ];
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleEventId()
    {
        return $this->googleEventId;
    }

    /**
     * @param mixed $googleEventId
     *
     * @return $this
     */
    public function setGoogleEventId($googleEventId)
    {
        $this->googleEventId = $googleEventId;

        return $this;
    }
}
