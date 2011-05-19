<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use \DateTime;

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
     * @mongodb:ReferenceOne(targetDocument="Visitor", inversedBy="visits")
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
     * @var string
     * @mongodb:String
     */
    private $localTime;
    /**
     * @var int
     * @mongodb:Int
     */
    private $localTimeZone;
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
     * @return VisitHit
     */
    public function getLastHit()
    {
        return $this->getHits()->last();
    }

    /**
     * @return VisitHit
     */
    public function getFirstHit()
    {
        return $this->getHits()->first();
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getFirstHit()->getReferer();
    }

    /**
     * @return VisitLink
     */
    public function getCurrentPage()
    {
        return $this->getLastHit()->getVisitLink();
    }

    public function getDuration()
    {
        return $this->getUpdatedAt()->format('U') - $this->getCreatedAt()->format('U');
    }

    public function getCurrentPageDuration()
    {
        return time() - $this->getLastHit()->getCreatedAt()->format('U');
    }

    public function getHostname()
    {
        $ip = $this->getRemoteAddr();
        $record = gethostbyaddr($ip);

        return $record;
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
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return string $localTime
     */
    public function getLocalTime()
    {
        return $this->localTime;
    }

    /**
     * @return int $localTimeZone
     */
    public function getLocalTimeZone()
    {
        return $this->localTimeZone;
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
     * @param string $localTime
     * @return void
     */
    public function setLocalTime($localTime)
    {
        $this->localTime = $localTime;
    }

    /**
     * @param int $localTimeZone
     * @return void
     */
    public function setLocalTimeZone($localTimeZone)
    {
        $this->localTimeZone = $localTimeZone;
    }

}