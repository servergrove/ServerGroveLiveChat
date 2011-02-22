<?php

namespace ServerGrove\SGLiveChatBundle\Form;

use Symfony\Component\Form\CheckboxField;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\HiddenField;

/**
 * Description of OperatorForm
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorForm extends Form
{

    protected function configure()
    {
        parent::configure();
        $this->add(new HiddenField('id'));
        $this->add(new TextField('name'));
        $this->add(new RepeatedField(new TextField('email')));
        $this->add(new RepeatedField(new PasswordField('passwd')));
        $this->add(new CheckboxField('isActive'));
    }

}