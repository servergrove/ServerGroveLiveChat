<?php

namespace ServerGrove\SGLiveChatBundle\Form;

use Symfony\Component\Form\CheckboxField;

use Symfony\Component\Form\TextField;

use Symfony\Component\Form\HiddenField;

use Symfony\Component\Form\Form;

/**
 * Description of OperatorDepartmentForm
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorDepartmentForm extends Form
{

    protected function configure()
    {
        parent::configure();
        $this->add(new HiddenField('id'));
        $this->add(new TextField('name'));
        $this->add(new CheckboxField('isActive'));
    }

}