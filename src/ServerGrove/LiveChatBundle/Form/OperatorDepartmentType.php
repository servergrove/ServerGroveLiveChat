<?php

namespace ServerGrove\LiveChatBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

/**
 * Description of OperatorDepartmentType
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorDepartmentType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array('label' => 'Name'));
        $builder->add('isActive', 'checkbox', array('label' => 'Is Active', 'required' => false));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'ServerGrove\LiveChatBundle\Document\Operator\Department',
        );
    }

}