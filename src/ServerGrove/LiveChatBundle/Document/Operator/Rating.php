<?php

namespace ServerGrove\SGLiveChatBundle\Document\Operator;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Description of Rating
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 *  collection="operator_rating",
 *  repositoryClass="ServerGrove\SGLiveChatBundle\Document\Operator\RatingRepository"
 * )
 */
class Rating
{

    /**
     * @var integer
     * @MongoDB\Id
     */
    private $id;
    /**
     * @var integer
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
     * @var ServerGrove\SGLiveChatBundle\Document\Session
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\SGLiveChatBundle\Document\Session")
     */
    private $session;
    /**
     * @var ServerGrove\SGLiveChatBundle\Document\Operator
     * @MongoDB\ReferenceOne(targetDocument="ServerGrove\SGLiveChatBundle\Document\Operator")
     */
    private $operator;

    /**
     * @MongoDB\PrePersist
     */
    public function registerCreatedDate()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
    }

    /**
     * @return integer $grade
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param integer $grade
     * @return void
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @return string $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     * @return void
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
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
     * @return integer $chatSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param integer $session
     * @return void
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return integer $operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param integer $operator
     * @return void
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

}