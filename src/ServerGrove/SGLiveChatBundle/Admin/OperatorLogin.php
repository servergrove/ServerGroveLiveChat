<?php

namespace ServerGrove\SGLiveChatBundle\Admin;

class OperatorLogin
{

    /**
     * @var string
     * @validation:Email
     * @validation:NotBlank
     */
    private $email;

    /**
     * @var string
     * @validation:MaxLength(20)
     * @validation:MinLength(6)
     * @validation:NotBlank
     */
    private $passwd;

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return the $passwd
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * @param string $passwd
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

}