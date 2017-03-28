<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FormRequest.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FormRequestRepository")
 */
class FormRequest
{
    use ORMBehaviors\Timestampable\Timestampable;

    const STATUS = ['pending', 'approved', 'rejected'];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FormRequestType
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="FormRequestType", inversedBy="requests")
     * @Groups({"Detail"})
     */
    private $type;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 255
     * )
     * @ORM\Column(type="string", length=255)
     * @Groups({"Detail"})
     */
    private $status;

    /**
     * @var User
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="User", inversedBy="formRequests")
     */
    private $user;

    /**
     * @var \DateTime
     * @Assert\Date()
     * @ORM\Column(type="datetime")
     * @Groups({"Detail"})
     */
    private $date;

    function __construct()
    {
        $this->setStatus("pending");
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
     * @param FormRequestType $type
     *
     * @return FormRequest
     */
    public function setType(FormRequestType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return FormRequestType
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
     * @return FormRequest
     */
    public function setStatus($status)
    {
        if (in_array($status, self::STATUS)) {
            $this->status = $status;
        }
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
     * @return FormRequest
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return FormRequest
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
