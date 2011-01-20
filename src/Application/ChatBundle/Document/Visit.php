<?php

namespace Application\ChatBundle\Document;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="visits")
 */
class Visit
{

    /**
     * @var integer
     * @mongodb:Id
     */
    private $id;

    /**
     * @mongodb:ReferenceOne(targetDocument="Visitor")
     */
    private $visitor;

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
     * @mongodb:Date
     */
    private $created_at;

    /**
     * @var string
     * @mongodb:Date
     */
    private $updated_at;

    /**
     * @var string
     * @mongodb:Timestamp
     */
    private $local_time;

    /**
     * @mongodb:PrePersist
     */
    public function registerCreatedDate()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
        $this->registerUpdatedDate();
    }

    /**
     * @mongodb:PreUpdate
     */
    public function registerUpdatedDate()
    {
        $this->setUpdatedAt(date('Y-m-d H:i:s'));
    }

    /**
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return field_type $visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string $remote_addr
     */
    public function getRemoteAddr()
    {
        return $this->remote_addr;
    }

    /**
     * @return string $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return string $updated_at
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @return string $local_time
     */
    public function getLocalTime()
    {
        return $this->local_time;
    }

    /**
     * @param field_type $visitor
     * @return void
     */
    public function setVisitor($visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * @param string $key
     * @return void
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @param string $remote_addr
     * @return void
     */
    public function setRemoteAddr($remote_addr)
    {
        $this->remote_addr = $remote_addr;
    }

    /**
     * @param string $created_at
     * @return void
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @param string $updated_at
     * @return void
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @param string $local_time
     * @return void
     */
    public function setLocalTime($local_time)
    {
        $this->local_time = $local_time;
    }

}