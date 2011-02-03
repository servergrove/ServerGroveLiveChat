<?php

namespace Application\ChatBundle\Document;

use Symfony\Component\Security\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\User\AccountInterface;
use Application\ChatBundle\Document\Operator\Department;

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
class Operator implements AccountInterface, PasswordEncoderInterface
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
    private $createdAt;
    /**
     * @var string
     * @mongodb:Date
     */
    private $updatedAt;
    /**
     * @var boolean
     * @mongodb:Field(type="boolean")
     */
    private $isOnline;
    /**
     * @var boolean
     * @mongodb:Field(type="boolean")
     */
    private $isActive;
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
    /**
     * @var Department[]
     * @mongodb:ReferenceMany(targetDocument="Application\ChatBundle\Document\Operator\Department")
     */
    private $departments;

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

    /**
     * @return boolean $isOnline
     */
    public function getIsOnline()
    {
        return $this->isOnline;
    }

    /**
     * @param boolean $isOnline
     * @return void
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;
    }

    /**
     * @return boolean $isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param boolean $isActive
     * @return void
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
        $this->passwd = $this->encodePassword($passwd, $this->getSalt());
    }

    /**
     * @return Department[] $departments
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    public function addDepartment(Department $department) {
        $this->departments[] = $department;
    }

    # -- AccountInterface implementation ----------------

    /**
     * @return string
     */
    public function __toString()
    {
        return strtr('(:id) :name, :email', array(
            ':email' => $this->getEmail(),
            ':name' => $this->getName(),
            ':id' => $this->getId()));
    }

    /**
     * @param AccountInterface $account
     * @return boolean
     */
    public function equals(AccountInterface $account)
    {
        return $account instanceof Operator && $account->getId() == $this->getId();
    }

    public function eraseCredentials()
    {

    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getPasswd();
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array(
            'ROLE_USER');
    }

    public function getSalt()
    {
        return __NAMESPACE__ . '\\' . __CLASS__;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function encodePassword($raw, $salt)
    {
        return md5(md5($raw) . '-' . $salt);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded == $this->encodePassword($raw, $salt);
    }

}