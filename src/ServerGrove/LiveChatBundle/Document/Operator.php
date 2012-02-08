<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ServerGrove\LiveChatBundle\Document\Operator\Department;

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
class Operator extends User implements UserInterface, PasswordEncoderInterface
{

    /**
     * @var boolean
     * @MongoDB\Field(type="boolean")
     */
    private $isOnline;

    /**
     * @var boolean
     * @MongoDB\Field(type="boolean")
     */
    private $isActive = true;

    /**
     * @var string
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $passwd;

    /**
     * @var ServerGrove\LiveChatBundle\Document\Operator\Rating
     * @MongoDB\ReferenceMany(targetDocument="ServerGrove\LiveChatBundle\Document\Operator\Rating")
     */
    private $ratings = array();

    /**
     * @var Department[]
     * @MongoDB\ReferenceMany(targetDocument="ServerGrove\LiveChatBundle\Document\Operator\Department")
     */
    private $departments;

    /** @MongoDB\String */
    private $salt;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }

    public function addRating(Operator\Rating $rating)
    {
        $this->ratings[] = $rating;
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
     *
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
     *
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

    public function addDepartment(Department $department)
    {
        $this->departments[] = $department;
    }

    public function setDepartments($departments)
    {
        $this->departments = $departments;
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
            'ROLE_USER'
        );
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function encodePassword($raw, $salt)
    {
        return md5(md5($raw).'-'.$salt);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded == $this->encodePassword($raw, $salt);
    }

}