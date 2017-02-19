<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="supervisor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserSupervisorRepository")
 */
class UserSupervisor extends User
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your name cannot contain a number"
     * )
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 190
     * )
     * @ORM\Column(name="first_name", type="string", length=190)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your lastname cannot contain a number"
     * )
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 190
     * )
     * @ORM\Column(name="last_name", type="string", length=190, nullable=true)
     *
     */
    private $lastName;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 255
     * )
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     *
     */
    private $address;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 190
     * )
     * @ORM\Column(name="phone_number", type="string", length=190, nullable=true)
     */
    private $phoneNumber;

    /**
     * @var \DateTime
     * @Assert\Date()
     * @ORM\Column(name="dateOfBirth", type="datetime")
     */
    private $dateOfBirth;


    /**
     * @var ArrayCollection|UserIntern[]
     * @ORM\OneToMany(targetEntity="UserIntern", mappedBy="sypervisor")
     */
    private $interns;

    public function __construct()
    {
        $this->interns = new ArrayCollection();
        $roles = array('ROLE_USER_SUPERVISOR');
        $json = json_encode($roles);
        $this->roles = $json;

    }

    /**
     * Set firstname
     *
     * @param string $name
     *
     * @return User
     */
    public function setFirstName($name)
    {
        $this->firstName = $name;

        return $this;
    }

    /**
     * Get first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return UserSupervisor
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return UserSupervisor
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone number
     *
     * @param string $phone_number
     *
     * @return UserSupervisor
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phoneNumber = $phone_number;

        return $this;
    }

    /**
     * Get phone number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return UserSupervisor
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }


    /**
     * @return ArrayCollection
     */
    public function getInterns()
    {
        return $this->interns;
    }

}
