<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * SurveyTypeSection.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyTypeSectionRepository")
 */
class SurveyTypeSection
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"group2"})
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
     * @ORM\Column(type="string", length=190, nullable=true)
     * @Groups({"group2"})
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank()
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
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @ORM\Column(type="integer")
     * @Groups({"group2"})
     */
    private $orderNumber;

    /**
     * @var SurveyType
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyType", inversedBy="sections")
     */
    private $surveyType;

    /**
     * @var ArrayCollection[SurveyQuestion]
     * @ORM\OneToMany(targetEntity="SurveyQuestion", mappedBy="surveyTypeSection", cascade={"persist", "remove"})
     * @Groups({"group2"})
     */
    private $questions;

    public function __construct()
    {
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
     * @return SurveyTypeSection
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
     * @return SurveyTypeSection
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
     * Set order number.
     *
     * @param int $number
     *
     * @return SurveyTypeSection
     */
    public function setOrderNumber($number)
    {
        $this->orderNumber = $number;

        return $this;
    }

    /**
     * Get order number.
     *
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set survey type.
     *
     * @param SurveyType $type
     *
     * @return SurveyTypeSection
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
     * Get Questions.
     *
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
