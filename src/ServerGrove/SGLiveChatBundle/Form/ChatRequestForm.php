<?php
namespace ServerGrove\SGLiveChatBundle\Form;

use Symfony\Component\Form\TextareaField;

use Symfony\Component\Form\Form;

class ChatRequestForm extends Form
{

    protected function configure()
    {
        parent::configure();

        $this->setDataClass('ServerGrove\\SGLiveChatBundle\\Chat\\ChatRequest');

        $this->add('name');
        $this->add('email');
        $this->add(new TextareaField('question'));
    }
}