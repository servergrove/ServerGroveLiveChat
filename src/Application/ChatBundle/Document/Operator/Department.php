<?php

namespace Application\ChatBundle\Document\Operator;

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
     * @mongodb:Boolean
     */
    private $isActive;

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

}