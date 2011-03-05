<?php

namespace ServerGrove\SGLiveChatBundle\Form;

use Symfony\Component\Form\TextareaField;

use Symfony\Component\Form\Form;

class OperatorLoginForm extends Form
{

    protected function configure()
    {
        parent::configure();

        $this->setDataClass('ServerGrove\\SGLiveChatBundle\\Admin\\OperatorLogin');

        $this->add('email');
        $this->add('passwd');
    }
}