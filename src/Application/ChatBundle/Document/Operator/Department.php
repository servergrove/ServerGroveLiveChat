<?php

namespace Application\ChatBundle\Document;

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
    private $is_active;

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
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

}