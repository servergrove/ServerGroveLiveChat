<?php

namespace ServerGrove\SGLiveChatBundle\Form;

use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Form\AbstractType;

class OperatorLoginType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email');
        $builder->add('passwd', 'password');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'ServerGrove\\SGLiveChatBundle\\Admin\\OperatorLogin');
    }
}