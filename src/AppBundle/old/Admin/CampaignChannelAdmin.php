<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CampaignChannelAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ->add('campaign', 'sonata_type_model', array(
                'class'    => 'AppBundle\Entity\Campaign',
                'property' => 'name'
            ))
        ->add('channel', 'sonata_type_model', array(
                'class'    => 'AppBundle\Entity\Channel',
                'property' => 'name'
            ))
        ;
   }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('campaign', 'associated_property')
            ->add('channel', 'associated_property')
        ;
    }

    public function toString($object)
    {
        return 'CampaignChannel'; // shown in the breadcrumb on the create view        
    }
}