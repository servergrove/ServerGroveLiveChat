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
    private $created_at;

    /**
     * @mongodb:String
     */
    private $referer;

    /**
     * @mongodb:Integer
     */
    private $visit_link_id;

    /**
     * @return the $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param field_type $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
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
     * @return the $visit_link_id
     */
    public function getVisitLinkId()
    {
        return $this->visit_link_id;
    }

    /**
     * @param field_type $visit_link_id
     */
    public function setVisitLinkId($visit_link_id)
    {
        $this->visit_link_id = $visit_link_id;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

}