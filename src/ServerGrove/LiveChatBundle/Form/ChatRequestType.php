<?php
namespace ServerGrove\LiveChatBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;

class ChatRequestType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name');
        $builder->add('email');
        $builder->add('question', 'textarea');

    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'ServerGrove\LiveChatBundle\Chat\ChatRequest');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'chat_request';
    }
}