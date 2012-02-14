<?php

namespace ServerGrove\LiveChatBundle\Document\Operator;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use ServerGrove\LiveChatBundle\Document\Operator;
use ServerGrove\LiveChatBundle\Document\Session;

/**
 * Represents the given rating to an operator
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 *  collection="operator_rating",
 *  repositoryClass="ServerGrove\LiveChatBundle\Document\Operator\RatingRepository"
 * )
 */
class Rating
{

    /**
     * @var Integer
     * @MongoDB\Id
     */
    private $id;

    /**
     * @var Integer
     * @MongoDB\Field(type="int")
     */
    private $grade;

    /**
     * @var string
     * @MongoDB\String
     */
    private $comments;

    /**
     * @var string
     * @MongoDB\Date
     */
    private $createdAt;

    /**
     * @var \ServerGrove\LiveChatBundle\Document\Session
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\LiveChatBundle\Document\Session")
     */
    private $session;

    /**
     * @var \ServerGrove\LiveChatBundle\Document\Operator
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\LiveChatBundle\Document\Operator")
     */
    private $operator;

    /**
     * Constructor
     *
     * @param \ServerGrove\LiveChatBundle\Document\Session $session
     */
    public function __construct(Session $session)
    {
        $this->setSession($session);
    }

    /**
     * @MongoDB\PrePersist
     */
    public function checkOperator()
    {
        if (is_null($this->operator) && $this->getSession()->getOperator() instanceof Operator) {
            $this->setOperator($this->getSession()->getOperator());
        }
    }

    /**
     * @MongoDB\PrePersist
     */
    public function registerCreatedDate()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
    }

    /**
     * @return Integer $grade
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param Integer $grade
     *
     * @return void
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     *
     * @return void
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
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
     *
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
     *
     * @return void
     */
    public function setSession($session)
    {
        if ($this->session instanceof Session && $session->getId() != $this->session->getId()) {
            throw new \BadMethodCallException('A session has been already set to this rating');
        }

        $this->session = $session;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function getOperator()
    {
        $this->checkOperator();

        return $this->operator;
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Operator $operator
     *
     * @return void
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return str_repeat('*', $this->getGrade()).' - '.$this->getComments();
    }

}