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
    const TYPE = ['personal day', 'overnight guest', 'sick day'];

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
     * @Assert\Choice(FormRequest::TYPE)
     * @ORM\Column(type="string", length=25)
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
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     * @Groups({"Detail"})
     */
    private $date;

    public function __construct()
    {
        $this->setStatus('pending');
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
     * @param $type
     *
     * @return FormRequest
     */
    public function setType($type)
    {
        if (in_array($type, self::TYPE)) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
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
        $this->date = new \DateTime($date);

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
