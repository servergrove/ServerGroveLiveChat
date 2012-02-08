<?php

namespace ServerGrove\LiveChatBundle\Document\Operator;

use ServerGrove\LiveChatBundle\Document\Operator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of Department
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 *  collection="operator_department",
 *  repositoryClass="ServerGrove\LiveChatBundle\Document\Operator\DepartmentRepository"
 * )
 */
class Department
{

    /**
     * @var integer
     * @MongoDB\Id
     */
    private $id;
    /**
     * @var string
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @var boolean
     * @MongoDB\Field(type="boolean")
     */
    private $isActive = true;
    /**
     * @var \ServerGrove\LiveChatBundle\Document\Operator[]
     * @MongoDB\ReferenceMany(targetDocument="ServerGrove\LiveChatBundle\Document\Operator")
     */
    private $operators = array();

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
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getOperators()
    {
        return $this->operators;
    }

    public function addOperator(Operator $operator)
    {
        $this->operators[] = $operator;
    }

    public function __toString()
    {
        return $this->getName();
    }

}