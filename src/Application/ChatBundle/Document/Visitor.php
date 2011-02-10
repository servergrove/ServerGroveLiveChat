<?php

namespace Application\ChatBundle\Document;

/**
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(
 *  collection="visitor",
 *  repositoryClass="Application\ChatBundle\Document\VisitorRepository"
 * )
 */
class Visitor
{

    /**
     * @var integer
     * @mongodb:Id
     */
    private $id;
    /**
     * @var string
     * @mongodb:String
     */
    private $agent;
    /**
     * @var string
     * @mongodb:String
     */
    private $name;
    /**
     * @var string
     * @mongodb:String
     */
    private $email;
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
     * @var string
     * @mongodb:Date
     */
    private $createdAt;
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
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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

    /**
     * @return the $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getVisits()
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit)
    {
        $this->visits[] = $visit;
    }

}