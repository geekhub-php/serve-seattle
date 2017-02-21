<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey
 *
 * @ORM\Table(name="survey")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyRepository")
 */
class Survey
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var SurveyType
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyType", inversedBy="surveys")
     */
    private $type;

    /**
     * @var UserIntern
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="UserIntern", inversedBy="surveys")
     */
    private $intern;

    /**
     * @var ArrayCollection|SurveyQuestion[]
     * @ORM\OneToMany(targetEntity="SurveyQuestion", mappedBy="survey")
     */
    private $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type.
     *
     * @param SurveyType $type
     *
     * @return Survey
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


    /**
     * Set intern.
     *
     * @param UserIntern $intern
     *
     * @return Survey
     */
    public function setIntern(UserIntern $intern)
    {
        $this->intern = $intern;

        return $this;
    }

    /**
     * Get intern.
     *
     * @return UserIntern
     */
    public function getIntern()
    {
        return $this->intern;
    }

    /**
     * Get Questions.
     *
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
