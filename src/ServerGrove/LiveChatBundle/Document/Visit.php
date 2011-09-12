<?php

namespace ServerGrove\LiveChatBundle\Document;

use \DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 * collection="visit",
 * repositoryClass="ServerGrove\LiveChatBundle\Document\VisitRepository"
 * )
 */
class Visit
{

    /**
     * @MongoDB\Id
     */
    private $id;
    /**
     * @MongoDB\ReferenceOne(targetDocument="Visitor", inversedBy="visits")
     */
    private $visitor;
    /**
     * @var string
     * @MongoDB\String
     */
    private $key;
    /**
     * @var string
     * @MongoDB\String
     */
    private $remoteAddr;
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
    private $localTime;
    /**
     * @var int
     * @MongoDB\Int
     */
    private $localTimeZone;
    /**
     * @MongoDB\EmbedMany(targetDocument="VisitHit")
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
        if ($this->getHits()->count() == 0) {
            return 0;
        }

        return time() - $this->getLastHit()->getCreatedAt()->format('U');
    }

    public function getHostname()
    {
        $ip = $this->getRemoteAddr();
        $record = gethostbyaddr($ip);

        return $record;
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