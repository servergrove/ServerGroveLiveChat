<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Description of Message
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\EmbeddedDocument
 */
class Message
{

    /**
     * @var integer
     * @MongoDB\Id
     */
    private $id;
    /**
     * @var User
     * @MongoDB\ReferenceOne(
     * 	discriminatorMap={
     * 		"operator"="Operator",
     * 		"visitor"="Visitor",
     * 		"admin"="Administrator"
     * 	}
     * )
     */
    private $sender;
    /**
     * @var Session
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\SGLiveChatBundle\Document\Session")
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
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param User $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    public function getSenderId()
    {
        if ($this->getSender()) {
            return $this->getSender()->getId();
        }

        return null;
    }

    public function getSenderName()
    {
        if (!$this->getSender()->getId()) {
            return $this->isOperator() ? 'Operator' : 'Visitor';
        }

        return $this->getSender()->getName();
    }

    public function isOperator()
    {
        return $this->getSender() instanceof Operator;
    }

    public function isVisitor()
    {
        return $this->getSender() instanceof Visitor;
    }

    /**
     * @return the $session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param Integer $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return the $createdAt
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
     * @return the $content
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