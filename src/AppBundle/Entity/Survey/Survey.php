<?php

namespace AppBundle\Entity\Survey;

use AppBundle\Entity\User;
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Survey\SurveyType", inversedBy="surveys")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $type;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var bool
     * @Assert\Type("bool")
     * @ORM\Column(type="boolean")
     */
    private $reviewed;

    /**
     * @var User
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="surveys")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @var ArrayCollection[SurveyAnswer]
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="survey", cascade={"persist", "remove"})
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->status = 'current';
        $this->reviewed = false;
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
     * Set status.
     *
     * @param string $status
     *
     * @return Survey
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set review.
     *
     * @param bool $review
     *
     * @return Survey
     */
    public function setReviewed($review)
    {
        $this->reviewed = $review;

        return $this;
    }

    /**
     * Get review.
     *
     * @return bool
     */
    public function isReviewed()
    {
        return $this->reviewed;
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
     * @param SurveyAnswer $answer
     *
     * @return Survey
     */
    public function addSurveyAnswer(SurveyAnswer $answer)
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setSurvey($this);
        }

        return $this;
    }

    /**
     * Get answers.
     *
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Get questions.
     *
     * @return array
     */
    public function getQuestions()
    {
        $sections = $this->getType()->getSections();
        foreach ($sections as $section) {
            foreach ($section->getQuestions() as $quest) {
                $questions[] = $quest;
            }
        }

        return $questions;
    }
}
