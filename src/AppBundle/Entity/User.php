<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface, \Serializable
{
    use ORMBehaviors\Timestampable\Timestampable;
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"Short"})
     */
    private $id;

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
     * @ORM\Column(type="string", length=190)
     * @Groups({"Short"})
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your lastname cannot contain a number"
     * )
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 190
     * )
     * @ORM\Column(type="string", length=190, nullable=true)
     * @Groups({"Short"})
     */
    private $lastName;

    /**
     * @var string
     * @Assert\Image()
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email(
     * )
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 250
     * )
     * @ORM\Column(type="string", length=250, unique=true)
     * @Groups({"Short"})
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 255
     * )
     * @Assert\NotBlank(groups={"registration"})
     */
    private $plainPassword;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     * @Groups({"Short"})
     */
    private $enabled = true;

    /**
     * @var
     * @ORM\Column(type="json_array")
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $apiToken;

    /**
     * @var ArrayCollection[Event]
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="users", cascade={"persist", "remove"})
     */
    private $events;

    /**
     * @var ArrayCollection[FormRequest]
     * @ORM\OneToMany(targetEntity="FormRequest", mappedBy="user")
     */
    private $formRequests;

    /**
     * @var ArrayCollection[Survey]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Survey\Survey", mappedBy="user")
     */
    private $surveys;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->formRequests = new ArrayCollection();
        $this->surveys = new ArrayCollection();
        $this->roles = array('ROLE_USER');
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
     * Get name.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Set firstname.
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
     * Get first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return User
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set api token.
     *
     * @param string $apiToken
     *
     * @return User
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param Event $event
     *
     * @return User
     */
    public function setEvent($event)
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addUser($this);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return ArrayCollection
     */
    public function getFormRequests()
    {
        return $this->formRequests;
    }

    /**
     * @return ArrayCollection
     */
    public function getSurveys()
    {
        return $this->surveys;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return User
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->enabled,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->firstName,
            $this->lastName,
            $this->email,
            $this->enabled) = unserialize($serialized);
    }
}
