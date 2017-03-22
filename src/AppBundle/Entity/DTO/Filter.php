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
     * @var DateTime
     */
    public $start;

    /**
     * @var DateTime
     */
    public $end;
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getDecision(): string
    {
        return $this->decision;
    }

    /**
     * @param string $decision
     */
    public function setDecision(string $decision)
    {
        $this->decision = $decision;
    }

    /**
     * @return DateTime
     */
    public function getStart(): DateTime
    {
        return $this->start;
    }

    /**
     * @param DateTime $start
     */
    public function setStart(DateTime $start)
    {
        $this->start = $start;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime
    {
        return $this->end;
    }

    /**
     * @param DateTime $end
     */
    public function setEnd(DateTime $end)
    {
        $this->end = $end;
    }
}
