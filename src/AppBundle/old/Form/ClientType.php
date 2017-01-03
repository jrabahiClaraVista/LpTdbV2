<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idClient')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('phone1')
            ->add('phone2')
            ->add('adress1')
            ->add('adress2')
            ->add('ville')
            ->add('codePostal')
            ->add('country')
            ->add('birthday')
            ->add('lastContact')
            ->add('phoneErr',       'checkbox', array('required' => false))
            ->add('adressErr',      'checkbox', array('required' => false))
            ->add('emailErr',       'checkbox', array('required' => false))
            ->add('optOutPhone',    'checkbox', array('required' => false))
            ->add('optOutSms',      'checkbox', array('required' => false))
            ->add('optOutEmail',    'checkbox', array('required' => false))
            ->add('optOutMail',     'checkbox', array('required' => false))
            ->add('score')
            ->add('createdAt')
            ->add('modifiedAt')
            ->add('active')
            ->add('vendorCode')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_client';
    }
}
