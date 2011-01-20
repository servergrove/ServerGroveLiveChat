<?php

namespace Application\ChatBundle\Document;

/**
 * Description of VisitLink
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="visit_link")
 */
class VisitLink
{

    /**
     * @mongodb:Id
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
     * @mongodb:String
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

}