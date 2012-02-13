<?php

namespace ServerGrove\LiveChatBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Department
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @MongoDB\Document(
 *  collection="operator_department",
 *  repositoryClass="ServerGrove\LiveChatBundle\Document\OperatorDepartmentRepository"
 * )
 */
class OperatorDepartment
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
    private $isActive;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Operator", inversedBy="departments")
     */
    private $operators;

    public function __construct()
    {
        $this->isActive = true;
        $this->operators = new ArrayCollection();
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
     *
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

    public function setOperators($operators)
    {
        $this->operators = $operators;
    }

    public function __toString()
    {
        return $this->getName();
    }

}