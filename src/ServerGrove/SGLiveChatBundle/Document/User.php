<?php

namespace ServerGrove\SGLiveChatBundle\Document;

/**
 * Description of User
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document
 * @mongodb:InheritanceType("COLLECTION_PER_CLASS")
 * @mongodb:HasLifecycleCallbacks
 */
abstract class User
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
    private $name;

    /**
     * @var string
     * @mongodb:String
     */
    private $email;

    /**
     * @var string
     * @mongodb:Date
     */
    private $createdAt;

    /**
     * @var string
     * @mongodb:Date
     */
    private $updatedAt;

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
     * @return Integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public abstract function getKind();

}