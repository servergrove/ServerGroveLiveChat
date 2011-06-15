<?php

namespace ServerGrove\SGLiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Description of CannedMessage
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(collection="canned_message",repositoryClass="ServerGrove\SGLiveChatBundle\Document\CannedMessageRepository")
 */
class CannedMessage
{

    /**
     * @var integer
     * @MongoDB\Id
     */
    private $id;
    /**
     * @var string
     * @MongoDB\String
     */
    private $content;
    /**
     * @var string
     * @MongoDB\String
     * @MongoDB\UniqueIndex(order="asc")
     */
    private $title;
    /**
     * @var string
     * @MongoDB\Date
     */
    private $createdAt;
    /**
     * @var string
     * @MongoDB\Date
     */
    private $updatedAt;

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
     * @return the $content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return the $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $vars
     * @return string
     */
    public function renderContent(array $vars)
    {
        $content = $this->getContent();

        foreach ($vars as $key => $value) {
            $content = \str_replace('%' . $key . '%', $value, $content);
        }

        return $content;
    }

}