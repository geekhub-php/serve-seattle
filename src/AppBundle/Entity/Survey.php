<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Survey.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SurveyRepository")
 */
class Survey
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"group1"})
     */
    private $id;

    /**
     * @var SurveyType
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="SurveyType", inversedBy="surveys")
     * @Groups({"group1"})
     */
    private $type;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     * @Groups({"group1"})
     */
    private $status = 'current';

    /**
     * @var User
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="User", inversedBy="surveys")
     */
    private $user;

    /**
     * @var ArrayCollection[SurveyAnswer]
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="survey", cascade={"persist", "remove"})
     */
    private $answers;

    public function __construct()
    {
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
     * Set type.
     *
     * @param SurveyType $type
     *
     * @return Survey
     */
    public function setType(SurveyType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return SurveyType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Survey
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Survey
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
     * Get answers.
     *
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Get DateTime.
     *
     * @return \DateTime
     * @Groups({"group1", "group2"})
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get DateTime.
     *
     * @return \DateTime
     * @Groups({"group1", "group2"})
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
