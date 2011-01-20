<?php

namespace Application\ChatBundle\Document;

/**
 * Description of Message
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="chat_message")
 * @mongodb:HasLifecycleCallbacks
 */
use Doctrine\ODM\MongoDB\Mapping\Date;

class Message
{

    /**
     * @var integer
     * @mongodb:Id
     */
    private $id;

    /**
     * @var integer
     * @mongodb:Integer
     */
    private $chat_operator_id;

    /**
     * @var string
     * @mongodb:Date
     */
    private $created_at;

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
     * @return the $chat_operator_id
     */
    public function getChatOperatorId()
    {
        return $this->chat_operator_id;
    }

    /**
     * @param Integer $chat_operator_id
     */
    public function setChatOperatorId($chat_operator_id)
    {
        $this->chat_operator_id = $chat_operator_id;
    }

    /**
     * @return the $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
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