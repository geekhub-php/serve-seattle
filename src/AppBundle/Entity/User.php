<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Survey\Survey;
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
     * @Groups({"Default", "Short", "Detail"})
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
     * @Groups({"Default", "Short", "Detail"})
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
     * @Groups({"Default", "Short", "Detail"})
     */
    private $lastName;

    /**
     * @var string
     * @ORM\OneToOne(
     *     targetEntity="AppBundle\Entity\S3\Image",
     *      cascade={"persist", "remove"},
     *      fetch="EAGER",
     *      orphanRemoval=true
     *     )
     * @Groups({"Short", "Detail"})
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
     * @Groups({"Short", "Detail"})
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
     * @var \DateTime
     * @Assert\Date()
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $linkExpiredAt;

    /**
     * @var ArrayCollection[Event]
     * @ORM\OneToMany(targetEntity="Event", mappedBy="user", cascade={"persist", "remove"})
     * @Groups({"Detail"})
     */
    private $events;

    /**
     * @var ArrayCollection[FormRequest]
     * @ORM\OneToMany(targetEntity="FormRequest", mappedBy="user")
     * @Groups({"Detail"})
     */
    private $formRequests;

    /**
     * @var ArrayCollection|$surveys[]
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
     * Set link expired date.
     *
     * @param \DateTime $linkExpiredAt
     *
     * @return User
     */
    public function setLinkExpiredAt($linkExpiredAt)
    {
        $this->linkExpiredAt = $linkExpiredAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLinkExpiredAt()
    {
        return $this->linkExpiredAt;
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
            $event->setUser($this);
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

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return User
     */
    public function addEvent(\AppBundle\Entity\Event $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \AppBundle\Entity\Event $event
     */
    public function removeEvent(\AppBundle\Entity\Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Add formRequest
     *
     * @param \AppBundle\Entity\FormRequest $formRequest
     *
     * @return User
     */
    public function addFormRequest(\AppBundle\Entity\FormRequest $formRequest)
    {
        $this->formRequests[] = $formRequest;

        return $this;
    }

    /**
     * Remove formRequest
     *
     * @param \AppBundle\Entity\FormRequest $formRequest
     */
    public function removeFormRequest(\AppBundle\Entity\FormRequest $formRequest)
    {
        $this->formRequests->removeElement($formRequest);
    }

    /**
     * Add survey
     *
     * @param Survey $survey
     *
     * @return User
     */
    public function addSurvey(Survey $survey)
    {
        $this->surveys[] = $survey;

        return $this;
    }


    /**
     * Get surveys
     *
     * @return ArrayCollection
     */
    public function getSurveys()
    {
        return $this->surveys;
    }
}
