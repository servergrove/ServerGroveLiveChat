<?php

namespace Application\ChatBundle\Document;

/**
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="visitors")
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
    private $remote_addr;

    /**
     * @var string
     * @mongodb:String
     */
    private $languages;

    /**
     * @var string
     * @mongodb:Date
     */
    private $created_at;

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
     * @return the $remote_addr
     */
    public function getRemoteAddr()
    {
        return $this->remote_addr;
    }

    /**
     * @param string $remote_addr
     */
    public function setRemoteAddr($remote_addr)
    {
        $this->remote_addr = $remote_addr;
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
     * @return the $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

}