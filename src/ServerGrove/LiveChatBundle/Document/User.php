<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of User
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\MappedSuperclass
 * @MongoDB\InheritanceType("COLLECTION_PER_CLASS")
 */
abstract class User
{
    /**
     * @var integer
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var string
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     * @MongoDB\String
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     * @MongoDB\Date
     */
    protected $createdAt;

    /**
     * @var string
     * @MongoDB\Date
     */
    protected $updatedAt;

    /**
     * @MongoDB\PrePersist
     */
    public function registerCreatedDate()
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'));
        $this->registerUpdatedDate();
    }

    /**
     * @MongoDB\PreUpdate
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
     *
     * @return \ServerGrove\LiveChatBundle\Document\User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     *
     * @return \ServerGrove\LiveChatBundle\Document\User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
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
     *
     * @return \ServerGrove\LiveChatBundle\Document\User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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
     *
     * @return \ServerGrove\LiveChatBundle\Document\User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public abstract function getKind();

}