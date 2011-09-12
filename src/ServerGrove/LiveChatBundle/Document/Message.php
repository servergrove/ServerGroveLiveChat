<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Represents a User message
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\EmbeddedDocument
 */
class Message
{

    /**
     * @var Integer
     * @MongoDB\Id
     */
    private $id;

    /**
     * @var User
     * @MongoDB\ReferenceOne(
     *     discriminatorMap={
     *         "operator"="Operator",
     *         "visitor"="Visitor",
     *         "admin"="Administrator"
     *     }
     * )
     */
    private $sender;

    /**
     * @var Session
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\LiveChatBundle\Document\Session")
     */
    private $session;

    /**
     * @var string
     * @MongoDB\Date
     */
    private $createdAt;

    /**
     * @var string
     * @MongoDB\String
     */
    private $content;

    /**
     * @MongoDB\PrePersist
     */
    public function registerCreatedDate()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
    }

    /**
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\User
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\User $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return Integer
     */
    public function getSenderId()
    {
        if ($this->getSender()) {
            return $this->getSender()->getId();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        $name = $this->getSender()->getName();
        if (empty($name)) {
            return $this->isOperator() ? 'Operator' : 'Visitor';
        }

        return $name;
    }

    /**
     * @return bool
     */
    public function isOperator()
    {
        return $this->getSender() instanceof Operator;
    }

    /**
     * @return bool
     */
    public function isVisitor()
    {
        return $this->getSender() instanceof Visitor;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

}