<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SurveyType
 *
 * @ORM\Table(name="survey_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyTypeRepository")
 */
class SurveyType
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
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 190
     * )
     * @ORM\Column(name="name", type="string", length=190, unique=true)
     */
    private $name;


    /**
     * @var ArrayCollection|SurveyQuestionContent[]
     *
     * @ORM\OneToMany(targetEntity="SurveyQuestionContent", mappedBy="survey_type")
     */
    private $questionContents;

    /**
     * @var ArrayCollection|Survey[]
     *
     * @ORM\OneToMany(targetEntity="Survey", mappedBy="type")
     */
    private $surveys;

    public function __construct()
    {
        $this->questions_contents = new ArrayCollection();
        $this->surveys = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return SurveyType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Get Questions contents.
     *
     * @return ArrayCollection
     */
    public function getQuestionsContents()
    {
        return $this->questionContents;
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
}
