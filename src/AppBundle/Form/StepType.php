<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\WorkflowStep;

class StepType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label' => 'step.edit.name'
            ));
        $builder->add('order', null, array(
            'label' => 'step.edit.order'
            ));
        $builder->add('color', null, array(
            'label' => 'step.edit.color'
            ));
        $builder->add('nextWorkflowStep', null, array(
            'label' => 'step.edit.nextWorkflowStep',
            'property' => 'name'
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\WorkflowStep',
        ));
    }

    public function getName()
    {
        return 'step';
    }
}