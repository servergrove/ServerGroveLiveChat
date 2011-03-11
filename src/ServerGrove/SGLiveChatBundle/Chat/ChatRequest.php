<?php

namespace ServerGrove\SGLiveChatBundle\Chat;

/**
 * A domain object for handling new Chat requests
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
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
     * Returns he name of the user that requests the chat session
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the user that requests the chat session
     *
     * @param string $name The user's name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the email account of the user that requests the chat session
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email account of the user that requests the chat session
     *
     * @param string $email The user's email account
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the question made by the user to request the chat session
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets the question made by the user to request the chat session
     *
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

}