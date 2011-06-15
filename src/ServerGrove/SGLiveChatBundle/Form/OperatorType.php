<?php

namespace ServerGrove\SGLiveChatBundle\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Description of OperatorType
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorType extends AbstractType
{

    private $dm;
    private $edit;

    public function __construct(DocumentManager $dm, $edit)
    {
        $this->dm = $dm;
        $this->edit = $edit;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array('label' => 'Name'));
        $builder->add('email', 'repeated', array('label' => 'e-mail'));
        $builder->add('passwd', 'repeated', array('type' => 'password', 'label' => 'Password', 'required' => !$this->edit));
        $builder->add('isActive', 'checkbox', array('label' => 'Is Active', 'required' => false));

        $departments = $this->dm->getRepository('SGLiveChatBundle:Operator\Department')->getDepartments();

        $choices = array();

        foreach ($departments as $department) {
            $choices[$department->getId()] = $department->getName();
        }

        $builder->add('departments', 'document', array(
            'document_manager' => $this->dm,
            'class' => 'SGLiveChatBundle:Operator\Department',
            'multiple' => true,
            'required' => false
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'ServerGrove\SGLiveChatBundle\Document\Operator',
        );
    }

}