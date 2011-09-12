<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 * collection="visitor",
 * repositoryClass="ServerGrove\LiveChatBundle\Document\VisitorRepository"
 * )
 */
class Visitor extends User
{

    /**
     * @var string
     * @MongoDB\String
     */
    private $agent;

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
     * @MongoDB\String
     */
    private $languages;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Visit")
     */
    private $visits = array();

    /**
     * @MongoDB\ReferenceOne(targetDocument="Visit", mappedBy="visitor", sort={"createdAt"="desc"})
     */
    private $lastVisit;

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param string $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getRemoteAddr()
    {
        return $this->remoteAddr;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return gethostbyaddr($this->remoteAddr);
    }

    /**
     * @param string $remoteAddr
     */
    public function setRemoteAddr($remoteAddr)
    {
        $this->remoteAddr = $remoteAddr;
    }

    /**
     * @return string
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param string $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return array
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * @param Visit $visit
     * @return void
     */
    public function addVisit(Visit $visit)
    {
        $this->visits[] = $visit;
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Visit;
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return 'Client';
    }

}