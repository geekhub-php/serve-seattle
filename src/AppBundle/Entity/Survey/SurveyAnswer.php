<?php

namespace AppBundle\Entity\Survey;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @var SurveyQuestion
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyQuestion", inversedBy="answers")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"group3", "group4"})
     */
    private $question;

    /**
     * @var Survey
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="answers")
     * @ORM\JoinColumn(onDelete="CASCADE")
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
     * @Groups({"group3", "group4"})
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
     * Set content.
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
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
