<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Request
 *
 * @ORM\Table(name="request")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RequestRepository")
 */
class Request
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
     * @var RequestType
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="RequestType", inversedBy="requests")
     */
    private $type;

    /**
     * @var int
     * @Assert\Range(
     *      min = 0,
     *      max = 2
     * )
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;

    /**
     * @var UserIntern
     * @Assert\Type("object")
     * @Assert\Valid
     * @ORM\ManyToOne(targetEntity="UserIntern", inversedBy="requests")
     */
    private $intern;


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
     * Set type.
     *
     * @param RequestType $type
     *
     * @return Request
     */
    public function setType(RequestType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return RequestType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Request
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set intern.
     *
     * @param UserIntern $intern
     *
     * @return Request
     */
    public function setIntern(UserIntern $intern)
    {
        $this->intern = $intern;

        return $this;
    }

    /**
     * Get intern.
     *
     * @return UserIntern
     */
    public function getIntern()
    {
        return $this->intern;
    }
}
