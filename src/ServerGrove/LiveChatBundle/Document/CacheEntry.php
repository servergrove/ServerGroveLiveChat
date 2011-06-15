<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Description of Session
 *
 * @author Pablo Godel<pablo@servergrove.com>
 * @MongoDB\Document(
 * collection="cache",
 * repositoryClass="ServerGrove\SGLiveChatBundle\Document\CacheRepository"
 * )
 */
class CacheEntry
{

    /**
     * @var integer
     * @MongoDB\Id
     */
    private $id;
    /**
     * @var string
     * @MongoDB\String
     * @MongoDB\Index(unique=true, order="asc")
     */
    private $key;
    /**
     * @var string
     * @MongoDB\String
     */
    private $data;
    /**
     * @var int
     * @MongoDB\int
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
        $this->expires = time() + $ttl;
    }

    public function isExpired()
    {
        return $this->expires <= time();
    }

}