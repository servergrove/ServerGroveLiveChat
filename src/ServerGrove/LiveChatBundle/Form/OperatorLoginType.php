<?php

namespace ServerGrove\LiveChatBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class OperatorLoginType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // although using "email" is correct, if no argument is
        // passed (or "text" is passed) as parameter 2, it won't render anything
        $builder->add('email', 'email', array(
            'label' => 'Email address',
            'attr' => array(
                'placeholder' => 'email@domain.com'
            )
        ));

        $builder->add('passwd', 'password', array(
            'label' => 'Password'
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'ServerGrove\\LiveChatBundle\\Admin\\OperatorLogin');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'login';
    }
}