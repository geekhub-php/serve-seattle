<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Admin.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdminRepository")
 */
class Admin implements UserInterface
{
    use ORMBehaviors\Timestampable\Timestampable;
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
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
     * @ORM\Column(type="string", length=190, unique=true)
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
     * @ORM\Column(type="string", length=250, unique=true)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 255
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var
     * @ORM\Column(type="json_array")
     */
    protected $roles;

    public function __construct()
    {
        $this->roles = array('ROLE_ADMIN');
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
     * Set username.
     *
     * @param string $name
     *
     * @return Admin
     */
    public function setUsername($name)
    {
        $this->userName = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->userName;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Admin
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
     * @return Admin
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

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

}
