<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ModuleAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab("Module")
                ->with("Content")
                    ->add('name', 'text')
            ->end();
   }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name', 'text')
            ->add('nbModules', 'integer')
            ->add('userModules', 'associated_property')
        ;
    }

    public function toString($object)
    {
        return 'Module ' . $object->getName() ; // shown in the breadcrumb on the create view        
    }
}