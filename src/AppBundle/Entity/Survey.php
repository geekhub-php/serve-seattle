<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyRepository")
 */
class Survey
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
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
     * @var User
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="User", inversedBy="surveys")
     */
    private $user;

    /**
     * @var ArrayCollection|SurveyQuestion[]
     * @ORM\ManyToMany(targetEntity="SurveyQuestion", mappedBy="surveys")
     */
    private $questions;

    /**
     * @var ArrayCollection|SurveyAnswer[]
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="survey")
     */
    private $answers;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->answers = new ArrayCollection();
    }

    /**
     * Get id.
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
     * Set user.
     *
     * @param User $user
     *
     * @return Survey
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param SurveyQuestion $question
     *
     * @return Survey
     */
    public function addQuestion(SurveyQuestion $question)
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->addSurvey($this);
        }

        return $this;
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

    /**
     * Get Questions.
     *
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
