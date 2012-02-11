<?php

namespace ServerGrove\LiveChatBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

/**
 * Class OperatorType
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorPasswordType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('passwd', 'repeated', array(
            'type'     => 'password',
            'label'    => 'Password',
            'required' => true,
            'options'  => array('attr' => array('autocomplete' => 'off'))
        ));
    }

    public function getName()
    {
        return 'operator';
    }
}
