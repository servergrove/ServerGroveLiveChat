<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Description of Session
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 * collection="chat_session",
 * repositoryClass="ServerGrove\LiveChatBundle\Document\SessionRepository"
 * )
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
     * @MongoDB\Id
     */
    private $id;
    /**
     * @var string
     * @MongoDB\String
     */
    private $sessionId;
    /**
     * @var string
     * @MongoDB\Date
     */
    private $createdAt;
    /**
     * @var string
     * @MongoDB\Date
     */
    private $updatedAt;
    /**
     * @var string
     * @MongoDB\String
     */
    private $remoteAddr;
    /**
     * @var Operator
     * @MongoDB\ReferenceOne(targetDocument="Visitor")
     */
    private $visitor;
    /**
     * @var Operator
     * @MongoDB\ReferenceOne(targetDocument="Operator")
     */
    private $operator;
    /**
     * @var Operator
     * @MongoDB\ReferenceOne(targetDocument="Visit")
     */
    private $visit;
    /**
     * @var string
     * @MongoDB\String
     */
    private $question;
    /**
     * @var integer
     * @MongoDB\Field(type="int")
     */
    private $statusId;
    /**
     * @var ChatMessage[]
     * @MongoDB\EmbedMany(targetDocument="Message")
     */
    private $messages = array();
    /**
     * @var ServerGrove\LiveChatBundle\Document\Operator\Rating
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\LiveChatBundle\Document\Operator\Rating")
     */
    private $rating;

    public function __construct()
    {
        $this->setStatusId(self::STATUS_WAITING);
    }

    /**
     * @MongoDB\PrePersist
     */
    public function registerFirstMessage()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
        $this->registerUpdatedDate();
    }

    /**
     * @MongoDB\PreUpdate
     */
    public function registerUpdatedDate()
    {
        $this->setUpdatedAt(date('Y-m-d H:i:s'));
    }

    /**
     * @MongoDB\PrePersist
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
     * @return ServerGrove\LiveChatBundle\Document\Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param ServerGrove\LiveChatBundle\Document\Operator $operator
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
     * @return ServerGrove\LiveChatBundle\Document\Rating
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
     * @param ServerGrove\LiveChatBundle\Document\Rating $rating
     * @return void
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

}