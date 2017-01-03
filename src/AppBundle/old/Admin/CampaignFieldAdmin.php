<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CampaignFieldAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('campaign', 'sonata_type_model', array(
                'class'    => 'AppBundle\Entity\Campaign',
                'property' => 'name'
            ))
        ->add('field', 'sonata_type_model', array(
                'class'    => 'AppBundle\Entity\Field',
                'property' => 'name'
            ))
        ;
   }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('campaign', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Campaign',
                'property' => 'name',
                'multiple' => true
        ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('campaign', 'associated_property')
            ->add('field', 'associated_property')
        ;
    }

    public function toString($object)
    {
        return 'CampaignField'; // shown in the breadcrumb on the create view        
    }
}