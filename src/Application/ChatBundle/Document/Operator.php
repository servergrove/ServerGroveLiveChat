<?php

namespace Application\ChatBundle\Document;

/**
 * Description of Operator
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(
 * collection="operators",
 * repositoryClass="Application\ChatBundle\Document\OperatorRepository"
 * )
 * @mongodb:InheritanceType("SINGLE_COLLECTION")
 * @mongodb:DiscriminatorField(fieldName="type")
 * @mongodb:DiscriminatorMap({"admin"="Administrator", "operator"="Operator"})
 * @mongodb:HasLifecycleCallbacks
 */
class Operator
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
     * @mongodb:UniqueIndex(order="asc")
     */
    private $email;

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
     * @var boolean
     * @mongodb:Boolean
     */
    private $is_online;

    /**
     * @var boolean
     * @mongodb:Boolean
     */
    private $is_active;

    /**
     * @var string
     * @mongodb:String
     */
    private $passwd;

    /**
     * @var Application\ChatBundle\Document\Operator\Rating
     * @mongodb:ReferenceMany(targetDocument="Application\ChatBundle\Document\Operator\Rating")
     */
    private $ratings = array();

    public function addRating(Operator\Rating $rating)
    {
        $this->ratings[] = $rating;
    }

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
     * @return string $created_at
     */
    public function getCreatedAt()
    {
        return $this->created_at;
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
     * @return string $updated_at
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
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
     * @return boolean $is_online
     */
    public function getIsOnline()
    {
        return $this->is_online;
    }

    /**
     * @param boolean $is_online
     * @return void
     */
    public function setIsOnline($is_online)
    {
        $this->is_online = $is_online;
    }

    /**
     * @return boolean $is_active
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * @param boolean $is_active
     * @return void
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
    }

    /**
     * @return string $passwd
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * @param string $passwd
     * @return void
     */
    public function setPasswd($passwd)
    {
        $this->passwd = md5($passwd);
    }

}