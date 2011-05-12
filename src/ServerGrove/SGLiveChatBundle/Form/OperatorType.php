<?php

namespace ServerGrove\SGLiveChatBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

/**
 * Description of OperatorType
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array('label' => 'Name'));
        $builder->add('email', 'repeated', array('label' => 'e-mail'));
        $builder->add('passwd', 'repeated', array('type' => 'password', 'label' => 'Password'));
        $builder->add('isActive', 'checkbox', array('label' => 'Is Active'));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'ServerGrove\SGLiveChatBundle\Document\Operator',
        );
    }

}