<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * SurveyType.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyTypeRepository")
 */
class SurveyType
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
     *      min = 2,
     *      max = 190
     * )
     * @ORM\Column(type="string", length=190, unique=true)
     * @Groups({"group1"})
     */
    private $name;

    /**
     * @var ArrayCollection[Survey]
     * @ORM\OneToMany(targetEntity="Survey", mappedBy="type")
     */
    private $surveys;

    /**
     * @var ArrayCollection[SurveyQuestion]
     * @ORM\OneToMany(targetEntity="SurveyQuestion", mappedBy="surveyType")
     * @Groups({"group3"})
     */
    private $questions;

    public function __construct()
    {
        $this->surveys = new ArrayCollection();
        $this->questions = new ArrayCollection();
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
     * Set name.
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Get Questions.
     *
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
