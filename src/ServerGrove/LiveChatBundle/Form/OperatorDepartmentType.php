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
        $builder->add('name');
        $builder->add('isActive');
    }

    public function getName()
    {
        return 'operator_department';
    }
}