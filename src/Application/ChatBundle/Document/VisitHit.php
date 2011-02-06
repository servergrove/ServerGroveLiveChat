<?php

namespace Application\ChatBundle\Document;

/**
 * Description of VisitHit
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="visits_hits")
 * @mongodb:HasLifecycleCallbacks
 */
class VisitHit
{

    /**
     * @mongodb:Id
     */
    private $id;

    /**
     * @mongodb:Date
     */
    private $createdAt;

    /**
     * @mongodb:String
     */
    private $referer;

    /**
     * @mongodb:ReferenceOne(targetDocument="Visit")
     */
    private $visit;

    /**
     * @mongodb:ReferenceOne(targetDocument="VisitLink")
     */
    private $visitLink;

    /**
     * @mongodb:PrePersist
     */
    public function registerCreatedDate()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
    }

    /**
     * @return the $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param field_type $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return the $referer
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * @param field_type $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * @return the $visitLink
     */
    public function getVisitLink()
    {
        return $this->visitLink;
    }

    /**
     * @param field_type $visitLink
     */
    public function setVisitLink(VisitLink $visitLink)
    {
        $this->visitLink = $visitLink;
        $this->getVisitLink()->addHit($this);
    }

    /**
     * @return the $visit
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param field_type $visit
     */
    public function setVisit(Visit $visit)
    {
        $this->visit = $visit;
        $this->getVisit()->addHit($this);
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

}