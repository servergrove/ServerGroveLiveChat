<?php

namespace ServerGrove\SGLiveChatBundle\Document;

/**
 * Description of CannedMessage
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="canned_message")
 */
class CannedMessage
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
    private $content;
    /**
     * @var string
     * @mongodb:String
     * @mongodb:UniqueIndex(order="asc")
     */
    private $title;
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