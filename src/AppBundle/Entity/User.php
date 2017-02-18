<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\MappedSuperclass */
abstract class User implements AdvancedUserInterface, \Serializable
{

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
     * @ORM\Column(name="user_name", type="string", length=190, unique=true)
     */
    private $userName;


    /**
     * @var string
     * @Assert\Email(
     *     checkMX = true
     * )
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 250
     * )
     * @ORM\Column(name="email", type="string", length=250, unique=true)
     *
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 255
     * )
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;


    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = true;

    /**
     * @var
     * @ORM\Column(type="json_array")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", unique=true)
     */
    private $apiToken;

    public function __construct()
    {
        $this->apiToken = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

    }

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
     * Set username
     *
     * @param string $name
     *
     * @return User
     */
    public function setUsername($name)
    {
        $this->userName = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->userName;
    }



    /**
     * Set email
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Set password
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
     * Get password
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
     * @param bool $status
     * @return $this
     */
    public function setIsEnabled($status)
    {
        $this->isActive = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    public function isAccountNonExpired()
    {
        // TODO: Implement isAccountNonExpired() method.
    }

    public function isAccountNonLocked()
    {
        // TODO: Implement isAccountNonLocked() method.
    }

    public function isCredentialsNonExpired()
    {
        // TODO: Implement isCredentialsNonExpired() method.
    }


    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }


    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $json = json_encode($roles);
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
     * @return
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }


    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->userName,
            $this->email,
            $this->isActive,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->userName,
            $this->email,
            $this->isActive,
            ) = unserialize($serialized);
    }
}
