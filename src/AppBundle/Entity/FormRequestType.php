<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RequestType.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FormRequestTypeRepository")
 */
class FormRequestType
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
     * @var string
     *
     * @Assert\Type("string")
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var ArrayCollection[FormRequest]
     *
     * @ORM\OneToMany(targetEntity="FormRequest", mappedBy="type")
     */
    private $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
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
     * @return FormRequestType
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
     * @return FormRequestType
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
     * Get Requests.
     *
     * @return ArrayCollection
     */
    public function getRequests()
    {
        return $this->requests;
    }
}
