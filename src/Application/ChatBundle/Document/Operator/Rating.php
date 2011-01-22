<?php

namespace Application\ChatBundle\Document\Operator;

/**
 * Description of Rating
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(
 *  collection="operator_rating",
 *  repositoryClass="Application\ChatBundle\Document\Operator\RatingRepository"
 * )
 */
class Rating
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
    private $grade;

    /**
     * @var string
     * @mongodb:String
     */
    private $comments;

    /**
     * @var string
     * @mongodb:Date
     */
    private $created_at;

    /**
     * @var integer
     * @mongodb:Integer
     */
    private $chat_session_id;

    /**
     * @var integer
     * @mongodb:Integer
     */
    private $chat_operator_id;

    /**
     * @mongodb:PrePersist
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
     * @return integer $chat_session_id
     */
    public function getChatSessionId()
    {
        return $this->chat_session_id;
    }

    /**
     * @param integer $chat_session_id
     * @return void
     */
    public function setChatSessionId($chat_session_id)
    {
        $this->chat_session_id = $chat_session_id;
    }

    /**
     * @return integer $chat_operator_id
     */
    public function getChatOperatorId()
    {
        return $this->chat_operator_id;
    }

    /**
     * @param integer $chat_operator_id
     * @return void
     */
    public function setChatOperatorId($chat_operator_id)
    {
        $this->chat_operator_id = $chat_operator_id;
    }

    /**
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

}