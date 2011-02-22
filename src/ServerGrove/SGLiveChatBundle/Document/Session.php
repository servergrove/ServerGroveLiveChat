<?php

namespace ServerGrove\SGLiveChatBundle\Document;

/**
 * Description of Session
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(
 * collection="chat_session",
 * repositoryClass="ServerGrove\SGLiveChatBundle\Document\SessionRepository"
 * )
 * @mongodb:HasLifecycleCallbacks
 */
class Session
{

    const STATUS_WAITING = 1;

    const STATUS_IN_PROGRESS = 2;

    const STATUS_CLOSED = 3;

    const STATUS_CANCELED = 4;

    const STATUS_INVITE = 5;

    private static $statuses = array(
        1 => 'Waiting',
        2 => 'In Progress',
        3 => 'Closed',
        4 => 'Canceled',
        5 => 'Invite');

    public function getStatus()
    {
        return self::$statuses[$this->getStatusId()];
    }

    /**
     * @var integer
     * @mongodb:Id
     */
    private $id;

    /**
     * @var string
     * @mongodb:String
     */
    private $sessionId;

    /**
     * @var string
     * @mongodb:Date
     */
    private $createdAt;

    /**
     * @var string
     * @mongodb:Date
     */
    private $updatedAt;

    /**
     * @var string
     * @mongodb:String
     */
    private $remoteAddr;

    /**
     * @var Operator
     * @mongodb:ReferenceOne(targetDocument="Visitor")
     */
    private $visitor;

    /**
     * @var Operator
     * @mongodb:ReferenceOne(targetDocument="Operator")
     */
    private $operator;

    /**
     * @var Operator
     * @mongodb:ReferenceOne(targetDocument="Visit")
     */
    private $visit;

    /**
     * @var string
     * @mongodb:String
     */
    private $question;

    /**
     * @var integer
     * @mongodb:Field(type="int")
     */
    private $statusId;

    /**
     * @var ChatMessage[]
     * @mongodb:EmbedMany(targetDocument="Message")
     */
    private $messages = array();

    /**
     * @var ServerGrove\SGLiveChatBundle\Document\Operator\Rating
     * @mongodb:ReferenceOne(targetDocument="ServerGrove\SGLiveChatBundle\Document\Operator\Rating")
     */
    private $rating;

    public function __construct()
    {
        $this->setStatusId(self::STATUS_WAITING);
    }

    /**
     * @mongodb:PrePersist
     */
    public function registerFirstMessage()
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

    /**
     * @mongodb:PrePersist
     */
    public function registerCreatedDate()
    {
        $question = $this->getQuestion();
        if (!empty($question) && $this->getVisitor()) {
            $this->addChatMessage($question, $this->getVisitor());
        }
    }

    public function addChatMessage($content, User $sender)
    {
        $m = new Message();
        $m->setContent($content);
        $m->setSender($sender);
        $m->setSession($this);
        $this->messages[] = $m;
    }

    public function start()
    {
        $this->setStatusId(self::STATUS_IN_PROGRESS);
    }

    public function close()
    {
        $this->setStatusId(self::STATUS_CLOSED);
    }

    public function cancel()
    {
        $this->setStatusId(self::STATUS_CANCELED);
    }

    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return string $sessionId
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     * @return void
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return string $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string $remoteAddr
     */
    public function getRemoteAddr()
    {
        return $this->remoteAddr;
    }

    /**
     * @param string $remoteAddr
     * @return void
     */
    public function setRemoteAddr($remoteAddr)
    {
        $this->remoteAddr = $remoteAddr;
    }

    /**
     * @return Visitor $visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @param Visitor $visitorId
     * @return void
     */
    public function setVisitor($visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param ServerGrove\SGLiveChatBundle\Document\Operator $operator
     * @return void
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    public function getOtherMember(User $user)
    {
        if ($user->getKind() == 'Operator') {
            return $this->getVisitor();
        }

        return $this->getOperator();
    }

    /**
     * @return Visit $visit
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param Visit $visit
     * @return void
     */
    public function setVisit($visit)
    {
        $this->visit = $visit;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return integer $statusId
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param integer $statusId
     * @return void
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Rating
     */
    public function getRating()
    {
        if (!$this->rating) {
            $this->rating = new Operator\Rating();
            $this->rating->setOperator($this->getOperator());
            $this->rating->setSession($this);
        }

        return $this->rating;
    }

    /**
     * @param ServerGrove\SGLiveChatBundle\Document\Rating $rating
     * @return void
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

}