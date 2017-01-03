<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SendEmailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('from', 'text')
            //->add('to', 'text')
            //->add('cc', 'text',     array('required' => false))
            //->add('bcc', 'text',    array('required' => false))
            ->add('password', 'password')
            ->add('subject', 'text')
            ->add('template', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => array(
                    'Sélectionner le modèle de votre choix' => '',
                    'Template 1 article' => '0',
                    'Template 2 articles' => '1'
                ),
                'choices_as_values' => true,
                'required' => false
            ))
            ->add('message', 'textarea')
            ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_send_email';
    }
}
