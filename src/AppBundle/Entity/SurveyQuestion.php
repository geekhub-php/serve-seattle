<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SurveyQuestion
 *
 * @ORM\Table(name="survey_question")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyQuestionRepository")
 */
class SurveyQuestion
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
     * @var Survey
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="questions")
     */
    private $survey;


    /**
     * @var UserIntern
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="UserIntern", inversedBy="requests")
     */
    private $intern;

    /**
     * @var SurveyQuestionContent
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyQuestionContent", inversedBy="questions")
     */
    private $questionContent;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 1000
     * )
     * @ORM\Column(name="body", type="text")
     */
    private $answer;

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
     * Set survey.
     *
     * @param Survey $survey
     *
     * @return SurveyQuestion
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
     * Set intern.
     *
     * @param UserIntern $intern
     *
     * @return SurveyQuestion
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
     * Set question content.
     *
     * @param SurveyQuestionContent $questionContent
     *
     * @return SurveyQuestion
     */
    public function setQuestionContent(SurveyQuestionContent $questionContent)
    {
        $this->questionContent = $questionContent;

        return $this;
    }

    /**
     * Get question content.
     *
     * @return SurveyQuestionContent
     */
    public function getQuectionContent()
    {
        return $this->questionContent;
    }

    /**
     * Set answer.
     *
     * @param string $answer
     *
     * @return SurveyQuestion
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
