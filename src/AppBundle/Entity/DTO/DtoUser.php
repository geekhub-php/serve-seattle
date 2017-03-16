<?php


namespace AppBundle\Entity\DTO;


use Symfony\Component\Validator\Constraints as Assert;

class DtoUser
{
    /**
     * @var
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @var
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
}
