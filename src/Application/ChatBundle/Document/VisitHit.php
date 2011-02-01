<?php

namespace Application\ChatBundle\Document;

/**
 * Description of VisitHit
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="visit_hit")
 */
class VisitHit
{

    /**
     * @var integer
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
     * @mongodb:Field(type="int")
     */
    private $visitLinkId;

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
     * @return the $visitLinkId
     */
    public function getVisitLinkId()
    {
        return $this->visitLinkId;
    }

    /**
     * @param field_type $visitLinkId
     */
    public function setVisitLinkId($visitLinkId)
    {
        $this->visitLinkId = $visitLinkId;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

}