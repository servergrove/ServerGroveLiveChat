<?php

namespace ServerGrove\SGLiveChatBundle\Document;

/**
 * Description of Session
 *
 * @author Pablo Godel<pablo@servergrove.com>
 * @mongodb:Document(
 * collection="cache",
 * repositoryClass="ServerGrove\SGLiveChatBundle\Document\CacheRepository"
 * )
 */
class CacheEntry
{

    /**
     * @var integer
     * @mongodb:Id
     */
    private $id;

    /**
     * @var string
     * @mongodb:String @Index(unique=true, order="asc")
     */
    private $key;

    /**
     * @var string
     * @mongodb:String
     */
    private $data;

    /**
     * @var int
     * @mongodb:int
     */
    private $expires;

    public function __construct()
    {
    }


    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $sessionId
     * @return void
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    public function setData($data)
    {
        $this->data = $data;
    }


    public function __toString()
    {
        return $this->getData();
    }

    public function setTtl($ttl = 60)
    {
        $this->expires = time()+$ttl;
    }

    public function isExpired()
    {
        return $this->expires <= time();
    }
}