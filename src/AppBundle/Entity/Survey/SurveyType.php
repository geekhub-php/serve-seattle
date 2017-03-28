<?php

namespace AppBundle\Entity\Survey;

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
     * @var string
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 500
     * )
     * @ORM\Column(type="string", length=500, nullable=true)
     * @Groups({"group2"})
     */
    private $description;

    /**
     * @var ArrayCollection[SurveyTypeSection]
     * @ORM\OneToMany(targetEntity="SurveyTypeSection", mappedBy="surveyType", cascade={"persist", "remove"})
     * @Groups({"group2"})
     */
    private $sections;

    /**
     * @var ArrayCollection[Survey]
     * @ORM\OneToMany(targetEntity="Survey", mappedBy="type", cascade={"persist", "remove"})
     */
    private $surveys;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->surveys = new ArrayCollection();
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
     * Set description.
     *
     * @param string $description
     *
     * @return SurveyType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get SurveysTypeSection.
     *
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
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
