<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Description of VisitHit
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\EmbeddedDocument
 */
class VisitHit
{

    /**
     * @MongoDB\Id
     */
    private $id;
    /**
     * @MongoDB\Date
     */
    private $createdAt;
    /**
     * @MongoDB\String
     */
    private $referer;
    /**
     * @MongoDB\ReferenceOne(targetDocument="VisitLink")
     */
    private $visitLink;

    /**
     * @MongoDB\PrePersist
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
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

}