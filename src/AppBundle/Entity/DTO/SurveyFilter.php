<?php

namespace AppBundle\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Survey\SurveyType;

class SurveyFilter
{
    /**
     * @var \DateTime
     * @Assert\Date()
     */
    private $start;

    /**
     * @var \DateTime
     * @Assert\Date()
     */
    private $end;

    /**
     * @var SurveyType
     * @Assert\Type("object")
     * @Assert\Valid
     */
    private $type;

    /**
     * Set start.
     *
     * @param \DateTime $date
     *
     * @return SurveyFilter
     */
    public function setStart($date)
    {
        $this->start = $date;

        return $this;
    }

    /**
     * Get start.
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end.
     *
     * @param \DateTime $date
     *
     * @return SurveyFilter
     */
    public function setEnd($date)
    {
        $this->end = $date;

        return $this;
    }
    /**
     * Get end.
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set type.
     *
     * @param SurveyType $type
     *
     * @return SurveyFilter
     */
    public function setType(SurveyType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return SurveyType
     */
    public function getType()
    {
        return $this->type;
    }
}
