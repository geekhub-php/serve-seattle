<?php

namespace AppBundle\Entity\Survey;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SurveyQuestion.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyQuestionRepository")
 */
class SurveyQuestion
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"group2", "group3", "group4"})
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
     * @Groups({"group2"})
     */
    private $title;

    /**
     * @var SurveyTypeSection
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyTypeSection", inversedBy="questions")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $surveyTypeSection;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @ORM\Column(type="integer")
     * @Groups({"group2"})
     */
    private $orderNumber;

    /**
     * @var array
     * @Assert\NotBlank()
     * @Assert\Type("array")
     * @ORM\Column(type="array")
     * @Groups({"group2"})
     */
    private $variants;

    /**
     * @var ArrayCollection[SurveyAnswer]
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="question", cascade={"persist", "remove"})
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->variants = array();
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
     * Set Survey Type Section.
     *
     * @param SurveyTypeSection $section
     *
     * @return SurveyQuestion
     */
    public function setSurveyTypeSection(SurveyTypeSection $section)
    {
        $this->surveyTypeSection = $section;

        return $this;
    }

    /**
     * Get Survey Type.
     *
     * @return SurveyTypeSection
     */
    public function getSurveyTypeSection()
    {
        return $this->surveyTypeSection;
    }

    /**
     * Set order number.
     *
     * @param int $number
     *
     * @return SurveyQuestion
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
     * Set variants.
     *
     * @param array $variants
     *
     * @return SurveyQuestion
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;

        return $this;
    }

    /**
     * Add variant.
     *
     * @param string $variant
     *
     * @return SurveyQuestion
     */
    public function addVariant($variant)
    {
        if (!in_array($variant, $this->variants)) {
            $this->variants[] = $variant;
        }

        return $this;
    }

    /**
     * Get variants.
     *
     * @return array
     */
    public function getVariants()
    {
        return $this->variants;
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
}
