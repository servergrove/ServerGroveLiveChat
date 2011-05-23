<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Date;

/**
 * Description of Message
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:EmbeddedDocument
 * @mongodb:HasLifecycleCallbacks
 */
class Message
{

    /**
     * @var integer
     * @mongodb:Id
     */
    private $id;

    /**
     * @var User
     * @mongodb:ReferenceOne(
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
     * @mongodb:ReferenceOne(targetDocument="ServerGrove\SGLiveChatBundle\Document\Session")
     */
    private $session;

    /**
     * @var string
     * @mongodb:Date
     */
    private $createdAt;

    /**
     * @var string
     * @mongodb:String
     */
    private $content;

    /**
     * @mongodb:PrePersist
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
    
    public function getSenderName() {
        if (!$this->getSender()->getId()) {
            return $this->isOperator() ? 'Operator' : 'Visitor';
        }
        
        return $this->getSender()->getName();
    }
    
    public function isOperator() {
        return $this->getSender() instanceof Operator;
    }
    
    public function isVisitor() {
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