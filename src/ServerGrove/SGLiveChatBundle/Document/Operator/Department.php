<?php

namespace ServerGrove\SGLiveChatBundle\Document\Operator;

use ServerGrove\SGLiveChatBundle\Document\Operator;

/**
 * Description of Department
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @mongodb:Document(collection="operator_department")
 */
class Department
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
     * @var boolean
     * @mongodb:Field(type="boolean")
     */
    private $isActive;
    /**
     * @var \ServerGrove\SGLiveChatBundle\Document\Operator[]
     * @mongodb:ReferenceMany(targetDocument="ServerGrove\SGLiveChatBundle\Document\Operator")
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

    public function getOperators() {
        return $this->operators;
    }

    public function addOperator(Operator $operator)
    {
        $this->operators[] = $operator;
    }

}