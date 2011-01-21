<?php

namespace Application\ChatBundle\Document;

/**
 * Description of Session
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="chat_session")
 * @mongodb:HasLifecycleCallbacks
 */
class Session
{

    /**
     * @var integer
     * @mongodb:Id
     */
    private $id;
    /**
     * @var string
     * @mongodb:String
     */
    private $session_id;
    /**
     * @var string
     * @mongodb:Date
     */
    private $created_at;
    /**
     * @var string
     * @mongodb:Date
     */
    private $updated_at;
    /**
     * @var string
     * @mongodb:String
     */
    private $remote_addr;
    /**
     * @var integer
     * @mongodb:Integer
     */
    private $visitor_id;
    /**
     * @var Operator
     * @mongodb:ReferenceOne(targetDocument="Operator")
     */
    private $operator;
    /**
     * @var integer
     * @mongodb:Integer
     */
    private $visit_id;
    /**
     * @var integer
     * @mongodb:Integer
     */
    private $status_id;
    /**
     * @var ChatMessage[]
     * @mongodb:EmbedMany(targetDocument="Message")
     */
    private $messages = array();

    /**
     * @mongodb:PrePersist
     */
    public function registerCreatedDate()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
        $this->registerUpdatedDate();
    }

    /**
     * @mongodb:PreUpdate
     */
    public function registerUpdatedDate()
    {
        $this->setUpdatedAt(date('Y-m-d H:i:s'));
    }

    public function addChatMessage($content, $operator = null)
    {
        $m = new Message();
        $m->setContent($content);
        if (!is_null($operator)) {
            $m->setOperator($operator);
        }
        $this->messages[] = $m;
    }

    public function getMessages() {
        return $this->messages;
    }
    /**
     * @return string $session_id
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * @param string $session_id
     * @return void
     */
    public function setSessionId($session_id)
    {
        $this->session_id = $session_id;
    }

    /**
     * @return string $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     * @return void
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string $updated_at
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     * @return void
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return string $remote_addr
     */
    public function getRemoteAddr()
    {
        return $this->remote_addr;
    }

    /**
     * @param string $remote_addr
     * @return void
     */
    public function setRemoteAddr($remote_addr)
    {
        $this->remote_addr = $remote_addr;
    }

    /**
     * @return integer $visitor_id
     */
    public function getVisitorId()
    {
        return $this->visitor_id;
    }

    /**
     * @param integer $visitor_id
     * @return void
     */
    public function setVisitorId($visitor_id)
    {
        $this->visitor_id = $visitor_id;
    }

    /**
     * @return integer $chat_operator_id
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param integer $chat_operator_id
     * @return void
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return integer $visit_id
     */
    public function getVisitId()
    {
        return $this->visit_id;
    }

    /**
     * @param integer $visit_id
     * @return void
     */
    public function setVisitId($visit_id)
    {
        $this->visit_id = $visit_id;
    }

    /**
     * @return integer $status_id
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * @param integer $status_id
     * @return void
     */
    public function setStatusId($status_id)
    {
        $this->status_id = $status_id;
    }

    /**
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

}