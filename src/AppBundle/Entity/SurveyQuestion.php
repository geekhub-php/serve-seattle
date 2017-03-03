<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SurveyQuestion.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyQuestionRepository")
 */
class SurveyQuestion
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
     * @Assert\Length(
     *      max = 500
     * )
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var SurveyType
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyType", inversedBy="questions")
     */
    private $surveyType;

    /**
     * @var ArrayCollection[Survey]
     * @ORM\ManyToMany(targetEntity="Survey", inversedBy="questions")
     */
    private $surveys;

    /**
     * @var ArrayCollection[SurveyAnswer]
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="question")
     */
    private $answers;

    public function __construct()
    {
        $this->surveys = new ArrayCollection();
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
     * Set title.
     *
     * @param string $title
     *
     * @return SurveyQuestion
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type.
     *
     * @param SurveyType $type
     *
     * @return SurveyQuestion
     */
    public function setSurveyType(SurveyType $type)
    {
        $this->surveyType = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return SurveyType
     */
    public function getSurveyType()
    {
        return $this->surveyType;
    }

    /**
     * @param Survey $survey
     *
     * @return SurveyQuestion
     */
    public function setSurveys(Survey $survey)
    {
        if (!$this->surveys->contains($survey)) {
            $this->surveys->add($survey);
            $survey->setQuestions($this);
        }

        return $this;
    }

    /**
     * Get Surveys.
     *
     * @return ArrayCollection
     */
    public function getSurveys()
    {
        return $this->surveys;
    }

    /**
     * Get Answers.
     *
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}
