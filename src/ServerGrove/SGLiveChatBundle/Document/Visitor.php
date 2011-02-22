<?php

namespace ServerGrove\SGLiveChatBundle\Document;

/**
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(
 * collection="visitor",
 * repositoryClass="ServerGrove\SGLiveChatBundle\Document\VisitorRepository"
 * )
 */
class Visitor extends User
{

    /**
     * @var string
     * @mongodb:String
     */
    private $agent;

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
     * @mongodb:String
     */
    private $languages;

    /**
     * @mongodb:ReferenceMany(targetDocument="Visit")
     */
    private $visits = array();

    /**
     * @return the $agent
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
     * @return the $key
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
     * @return the $remoteAddr
     */
    public function getRemoteAddr()
    {
        return $this->remoteAddr;
    }

    /**
     * @param string $remoteAddr
     */
    public function setRemoteAddr($remoteAddr)
    {
        $this->remoteAddr = $remoteAddr;
    }

    /**
     * @return the $languages
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

    public function getVisits()
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit)
    {
        $this->visits[] = $visit;
    }

    public function getKind()
    {
        return 'Client';
    }

}