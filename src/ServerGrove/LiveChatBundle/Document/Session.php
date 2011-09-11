<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Represents a chat session
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
        self::STATUS_WAITING => 'Waiting',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_CLOSED => 'Closed',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_INVITE => 'Invite'
    );

    /**
     * Returns a string representation of the status
     * @return string
     */
    public function getStatus()
    {
        return self::$statuses[$this->getStatusId()];
    }

    /**
     * @var Integer
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
     * @var \ServerGrove\LiveChatBundle\Document\Operator
     * @MongoDB\ReferenceOne(targetDocument="Visitor")
     */
    private $visitor;

    /**
     * @var \ServerGrove\LiveChatBundle\Document\Operator
     * @MongoDB\ReferenceOne(targetDocument="Operator")
     */
    private $operator;

    /**
     * @var \ServerGrove\LiveChatBundle\Document\Operator
     * @MongoDB\ReferenceOne(targetDocument="Visit")
     */
    private $visit;

    /**
     * @var string
     * @MongoDB\String
     */
    private $question;

    /**
     * @var Integer
     * @MongoDB\Field(type="int")
     */
    private $statusId;

    /**
     * @var array
     * @MongoDB\EmbedMany(targetDocument="Message")
     */
    private $messages = array();

    /**
     * @var \ServerGrove\LiveChatBundle\Document\Operator\Rating
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\LiveChatBundle\Document\Operator\Rating")
     */
    private $rating;

    /**
     * Creates a new instance of a chat session
     *
     * @param \ServerGrove\LiveChatBundle\Document\Visit $visit
     * @param string $question
     * @param int $status
     * @return Session
     */
    public static function create(Visit $visit, $question, $status)
    {
        $session = new self();

        $session->setVisit($visit);
        $session->setVisitor($visit->getVisitor());
        $session->setQuestion($question);
        $session->setStatusId($status);
        $session->setRemoteAddr($visit->getVisitor()->getRemoteAddr());

        return $session;
    }

    public function __construct()
    {
        $this->setStatusId(self::STATUS_WAITING);
    }

    /**
     * @MongoDB\PrePersist
     */
    public function registerCreatedDate()
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
    public function registerFirstMessage()
    {
        $question = $this->getQuestion();
        if (!empty($question) && $this->getVisitor()) {
            $this->addChatMessage($question, $this->getVisitor());
        }
    }

    /**
     * @param $content
     * @param \ServerGrove\LiveChatBundle\Document\User $sender
     * @return void
     */
    public function addChatMessage($content, User $sender)
    {
        $m = new Message();
        $m->setContent($content);
        $m->setSender($sender);
        $m->setSession($this);
        $this->messages[] = $m;
    }

    /**
     * Starts a chat session
     *
     * @return void
     */
    public function start()
    {
        $this->setStatusId(self::STATUS_IN_PROGRESS);
    }

    /**
     * Closes a chat session
     *
     * @return void
     */
    public function close()
    {
        $this->setStatusId(self::STATUS_CLOSED);
    }

    /**
     * Cancel a chat session
     *
     * @return void
     */
    public function cancel()
    {
        $this->setStatusId(self::STATUS_CANCELED);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return string
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
     * @return string
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
     * @return string
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
     * @return \ServerGrove\LiveChatBundle\Document\Visitor $visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Visitor $visitor
     * @return void
     */
    public function setVisitor($visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Operator $operator
     * @return void
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\User $user
     * @return \ServerGrove\LiveChatBundle\Document\User
     */
    public function getOtherMember(User $user)
    {
        if ($user->getKind() == 'Operator') {
            return $this->getVisitor();
        }

        return $this->getOperator();
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Visit $visit
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Visit $visit
     * @return void
     */
    public function setVisit($visit)
    {
        $this->visit = $visit;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param string $question
     * @return void
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return Integer
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param Integer $statusId
     * @return void
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Operator\Rating
     */
    public function getRating()
    {
        if (!$this->rating) {
            $this->rating = new Operator\Rating($this);
        }

        return $this->rating;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Operator\Rating $rating
     * @return void
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

}