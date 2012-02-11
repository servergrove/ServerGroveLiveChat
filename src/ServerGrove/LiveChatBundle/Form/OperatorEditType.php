<?php

namespace ServerGrove\LiveChatBundle\Form;

use Symfony\Component\Form\FormBuilder;

/**
 * Class OperatorEditType
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorEditType extends OperatorType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('passwd');
    }
}
