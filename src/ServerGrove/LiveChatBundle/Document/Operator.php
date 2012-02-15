<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Operator
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 * collection="operator",
 * repositoryClass="ServerGrove\LiveChatBundle\Document\OperatorRepository"
 * )
 * @MongoDB\InheritanceType("SINGLE_COLLECTION")
 * @MongoDB\DiscriminatorField(fieldName="type")
 * @MongoDB\DiscriminatorMap({"admin"="Administrator", "operator"="Operator"})
 */
class Operator extends User implements UserInterface, \Serializable
{

    /**
     * @var boolean
     * @MongoDB\Field(type="boolean")
     */
    protected $isOnline;

    /**
     * @var boolean
     * @MongoDB\Field(type="boolean")
     */
    protected $isActive = true;

    /**
     * @var string
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    protected $passwd;

    /**
     * @MongoDB\ReferenceMany(targetDocument="ServerGrove\LiveChatBundle\Document\Operator\Rating")
     */
    protected $ratings;

    /**
     * @MongoDB\ReferenceMany(targetDocument="OperatorDepartment", inversedBy="operators")
     */
    protected $departments;

    /** @MongoDB\String */
    protected $salt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->departments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    /**
     * @param Operator\Rating $rating
     *
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function addRating(Operator\Rating $rating)
    {
        $this->ratings->add($rating);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRatings()
    {
        return $this->ratings;
    }

    /**
     * @param $ratings
     *
     * @return Operator
     */
    public function setRatings($ratings)
    {
        $this->ratings = $ratings;

        return $this;
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
     *
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function setIsOnline($isOnline)
    {
        $this->isOnline = $isOnline;

        return $this;
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
     *
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
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
     *
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * @param OperatorDepartment $department
     *
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function addDepartment(OperatorDepartment $department)
    {
        $this->departments->add($department);

        return $this;
    }

    /**
     * @param $departments
     *
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    public function setDepartments($departments)
    {
        $this->departments = $departments;

        return $this;
    }

    public function getKind()
    {
        return 'Operator';
    }

    # -- AccountInterface implementation ----------------

    /**
     * @return string
     */
    public function __toString()
    {
        return strtr('(:id) :name, :email', array(
            ':email' => $this->getEmail(),
            ':name'  => $this->getName(),
            ':id'    => $this->getId()
        ));
    }

    /**
     * @param AccountInterface $account
     *
     * @return boolean
     */
    public function equals(UserInterface $account)
    {
        return $account instanceof Operator
            && $account->getId() == $this->getId()
            && $account->getUsername() == $this->getUsername();
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
        return array('ROLE_OPERATOR');
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or &null;
     */
    public function serialize()
    {
        return @serialize(
            array(
                'id'          => $this->getId(),
                'name'        => $this->getName(),
                'active'      => $this->getIsActive(),
                'email'       => $this->getEmail(),
                'ratings'     => $this->getRatings(),
                'departments' => $this->getDepartments(),
                'online'      => $this->getIsOnline(),
                'kind'        => $this->getKind(),
                'passwd'      => $this->getPasswd(),
                'roles'       => $this->getRoles(),
                'salt'        => $this->getSalt(),
                'created'     => $this->getCreatedAt(),
                'updated'     => $this->getUpdatedAt()
            )
        );
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     *
     * @return mixed the original value unserialized.
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->id = $data['id'];
        $this->salt = $data['salt'];

        $this->setIsActive($data['active'])
            ->setRatings($data['ratings'])
            ->setPasswd($data['passwd'])
            ->setDepartments($data['departments'])
            ->setIsOnline($data['online'])
            ->setCreatedAt($data['created'])
            ->setUpdatedAt($data['updated'])
            ->setEmail($data['email'])
            ->setName($data['name']);
    }
}