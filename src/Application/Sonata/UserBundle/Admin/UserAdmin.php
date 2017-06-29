<?php

namespace Application\Sonata\UserBundle\Admin;

use Sonata\UserBundle\Admin\Model\UserAdmin as SonataUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class UserAdmin extends SonataUserAdmin
{
    /**
        * {@inheritdoc}
        */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);

        $formMapper
            ->with('General')
                ->add('id', 'integer')
                ->add('webMail', 'choice', array(
                        'choices' => array(
                            ''          => ''    ,
                            'gmail'     => 'Gmail.com',
                            'outlook'   => 'Outlook.com',
                            ),
                        'required' => false,
                        'label' => 'Web mail'
                        )
                    )
                ->add('dr', 'text', array(
                       'label' => 'DR',
                       'required' => false,
                    )
                )
                ->add('brand', 'text', array(
                       'label' => 'Brand',
                       'required' => false,
                    )
                ) 
                ->add('nomVendeur', 'text', array(
                       'label' => 'nom Vendeur',
                       'required' => false,
                    )
                )
                ->add('prenomVendeur', 'text', array(
                       'label' => 'Prenom Vendeur',
                       'required' => false,
                    )
                )                
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        parent::configureDatagridFilters($datagridMapper);
        $datagridMapper->add('dr', null, array(
                'label' => 'DR'
                )
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $listMapper
            ->add('dr', 'text', array(
                'label' => 'DR'
                )
            )
            ->add('brand', 'text', array(
                'label' => 'Brand'
                )
            )
            ->add('webMail', 'text', array(
                'label' => 'Web mail'
                )
            )
            ->add('id', 'integer', array(
                'label' => 'Id'
                )
            )
            //->add('userModules', 'associated_property')
        ;
    }
}