<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use AppBundle\Form\ImageType;

class CampaignFileAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab("General informations")
                ->with("Content")
                    ->add('name', 'text')
                    ->add('brand', 'text', array('required' => false, 'label' => "Brand (Uppercase)"))
                    ->add('description', 'textarea', array('required' => false))
                ->end()
                ->with("Kpis Module")
                    ->add( 'image', new ImageType(), array('required' => false,'label' => 'Fichier') )
                ->end()
                ->with("Dates")
                    ->add('date', 'date')
                ->end()
            ->end()
        ;
        
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('description');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name')
            ->add('brand')
            ->add('date')
            ->add('description')
            ->add('image.url')
        ;
    }

    public function toString($object)
    {
        return 'CampaignFileAdmin' . $object->getName() ; // shown in the breadcrumb on the create view        
    }
}