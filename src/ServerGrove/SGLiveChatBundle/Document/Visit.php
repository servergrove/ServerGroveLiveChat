<?php

namespace ServerGrove\SGLiveChatBundle\Document;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(
 * collection="visit",
 * repositoryClass="ServerGrove\SGLiveChatBundle\Document\VisitRepository"
 * )
 * @mongodb:HasLifecycleCallbacks
 */
class Visit
{

    /**
     * @mongodb:Id
     */
    private $id;

    /**
     * @mongodb:ReferenceOne(targetDocument="Visitor")
     */
    private $visitor;

    /**
     * @var string
     * @mongodb:String
     */
    private $key;

    /**
     * @var string
     * @mongodb:String
     */
    private $remoteAddr;

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
     * @var int
     * @mongodb:Number
     */
    private $localTime;

    /**
     * @mongodb:EmbedMany(targetDocument="VisitHit")
     */
    private $hits;

    public function getHits()
    {
        return $this->hits;
    }

    public function addHit(VisitHit $hit)
    {
        $this->hits[] = $hit;
    }

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

    /**
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Visitor $visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string $remoteAddr
     */
    public function getRemoteAddr()
    {
        return $this->remoteAddr;
    }

    /**
     * @return string $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return int $localTime
     */
    public function getLocalTime()
    {
        return $this->localTime;
    }

    /**
     * @param field_type $visitor
     * @return void
     */
    public function setVisitor(Visitor $visitor)
    {
        $this->visitor = $visitor;
        $visitor->addVisit($this);
    }

    /**
     * @param string $key
     * @return void
     */
    public function setKey($key)
    {
        $this->key = $key;
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
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
     * @param int $localTime
     * @return void
     */
    public function setLocalTime($localTime)
    {
        $this->localTime = $localTime;
    }

}