<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Description of VisitLink
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 *  collection="visit_link",
 *  repositoryClass="ServerGrove\SGLiveChatBundle\Document\VisitLinkRepository"
 * )
 */
class VisitLink
{

    /**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @MongoDB\String
     */
    private $url;

    /**
     * @return the $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param field_type $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @MongoDB\ReferenceMany(targetDocument="VisitHit")
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

}