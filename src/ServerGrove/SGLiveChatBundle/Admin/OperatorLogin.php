<?php

namespace ServerGrove\SGLiveChatBundle\Admin;

/**
 * Simple domain object for handling admin
 * access to the backend
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
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
     * @return the $email The email account associated to the operator
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email The email account associated to the operator
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return the $passwd The operator's encoded password
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * @param string $passwd The operator's password
     * @return void
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

}