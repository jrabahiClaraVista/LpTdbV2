<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CampaignType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('startDate')
            ->add('endDate')
            ->add('state')
            ->add('nbClients')
            ->add('nbContacted')
            ->add('nbOptOut')
            ->add('active')
            ->add('channelOne')
            ->add('channelTwo')
            ->add('channelThree')
            ->add('channelFour')
            ->add('txO')
            ->add('txC')
            ->add('emailsSent')
            ->add('smsSent')
            ->add('mailsSent')
            ->add('submit', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Campaign'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_campaign';
    }
}
