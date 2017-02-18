<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SurveyQuestionContent
 *
 * @ORM\Table(name="survey_question_content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyQuestionContentRepository")
 */
class SurveyQuestionContent
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
     * @ORM\ManyToOne(targetEntity="SurveyType", inversedBy="question_contents")
     */
    private $surveyType;

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
     * @var ArrayCollection|SurveyQuestion[]
     *
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
     * Set survey type.
     *
     * @param SurveyType $survey_type
     *
     * @return SurveyQuestionContent
     */
    public function setSurveyType(SurveyType $survey_type)
    {
        $this->surveyType = $survey_type;

        return $this;
    }

    /**
     * Get survey type.
     *
     * @return SurveyType
     */
    public function getSurveyType()
    {
        return $this->surveyType;
    }


    /**
     * Set content.
     *
     * @param string $content
     *
     * @return SurveyQuestionContent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get questions.
     *
     * @return ArrayCollection
     */
    public function getAnswer()
    {
        return $this->questions;
    }


}
