<?php

namespace AppBundle\Entity\DTO;

use DateTime;

class Filter
{

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $type
     */
    public $type;

    /**
     * @var string $decision
     */
    public $decision;

    /**
     * @var string
     */
    public $start;

    /**
     * @var string
     */
    public $end;

    public function getStart()
    {
        $date = new DateTime($this->start);
        return $this->start = $date->format('Y-m-d H:i:s');
    }

    public function getEnd()
    {
        $date = new DateTime($this->end);
        return $this->end = $date->format('Y-m-d H:i:s');
    }
}
