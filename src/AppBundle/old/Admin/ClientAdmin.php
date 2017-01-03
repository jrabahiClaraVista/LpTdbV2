<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ClientAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('idClient', 'text')
            ->add('vendorCode', 'text')
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->add('email', 'text', array('required' => false))
            ->add('phone1', 'text', array('required' => false))
            ->add('phone2', 'text', array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('recipients', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Recipient',
                'property' => 'campaign_name',
                'multiple' => true
            ))
            ->add('firstname')
            ->add('lastname')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('idClient')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('recipients', 'associated_property')
            ->add('phone1')
            ->add('phone2')
            ->add('optOutEmail')
            ->add('birthday','date', array(
                'format' => 'd-m-Y'
                ))
            ->add('modifiedAt','date', array(
                'format' => 'd-m-Y'
            ))
        ;
    }

    public function toString($object)
    {
        return "Client : " . $object->getFullName();
            //: 'Client boubou'; // shown in the breadcrumb on the create view*/        
    }
}