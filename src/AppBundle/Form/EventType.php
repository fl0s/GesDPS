<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label' => 'form.event.name'
            ));
        $builder->add('comment', null, array(
            'label' => 'form.event.comment'
            ));
        $builder->add('date', 'datetime', array(
            'date_widget'   => 'single_text',
            'time_widget'   => 'single_text',
            'label'         => 'form.event.date'
            ));
        $builder->add('steps', 'entity', array(
                'class'     => 'AppBundle:WorkflowStep',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.order', 'ASC');
                },
                'property'  => 'name',
                'expanded'  => true,
                'multiple'  => true,
                'label'     => 'form.event.step'
            ));
        $builder->add('manager', 'entity', array(
            'class'     => 'AppBundle:User',
            'property'  => 'username',
            'label'     => 'form.event.manager'
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Event',
        ));
    }

    public function getName()
    {
        return 'event';
    }
}