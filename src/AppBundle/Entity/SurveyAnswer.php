<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SurveyAnswer.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyAnswerRepository")
 */
class SurveyAnswer
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
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var SurveyQuestion
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyQuestion", inversedBy="answers")
     */
    private $question;

    /**
     * @var User
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="User", inversedBy="answers")
     */
    private $user;

    /**
     * @var Survey
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="answers")
     */
    private $survey;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 1000
     * )
     * @ORM\Column(name="body", type="text")
     */
    private $content;

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
     * @param string $type
     *
     * @return SurveyAnswer
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set survey question.
     *
     * @param SurveyQuestion $question
     *
     * @return SurveyAnswer
     */
    public function setQuestion(SurveyQuestion $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get survey question.
     *
     * @return SurveyQuestion
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return SurveyAnswer
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
     * Set survey.
     *
     * @param Survey $survey
     *
     * @return SurveyAnswer
     */
    public function setSurvey(Survey $survey)
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get survey.
     *
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set type.
     *
     * @param string $content
     *
     * @return SurveyAnswer
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
