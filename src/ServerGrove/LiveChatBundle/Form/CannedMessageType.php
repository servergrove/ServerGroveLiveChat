<?php

namespace ServerGrove\LiveChatBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Description of CannedMessageType
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class CannedMessageType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('title', 'text', array('label' => 'Title'));
        $builder->add('content', 'textarea', array('label' => 'Content'));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'ServerGrove\LiveChatBundle\Document\CannedMessage'
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    function getName()
    {
        return 'canned_message';
    }
}