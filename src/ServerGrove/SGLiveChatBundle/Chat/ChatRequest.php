<?php

namespace ServerGrove\SGLiveChatBundle\Chat;

class ChatRequest
{

    /**
     * @var string
     * @validation:MaxLength(150)
     * @validation:MinLength(6)
     * @validation:NotBlank
     */
    private $name;

    /**
     * @var string
     * @validation:Email
     * @validation:NotBlank
     */
    private $email;

    /**
     * @var string
     * @validation:NotBlank
     */
    private $question;

    /**
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

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
     * @return the $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

}