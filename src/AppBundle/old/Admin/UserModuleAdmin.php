<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserModuleAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', 'sonata_type_model', array(
                'class'    => 'Application\Sonata\UserBundle\Entity\User',
                'property' => 'username'
            ))
            ->add('module', 'sonata_type_model', array(
                'class'    => 'AppBundle\Entity\Module',
                'property' => 'name'
            ))
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('module', null, array(), 'entity', array(
                'class'    => 'AppBundle\Entity\Module',
                'property' => 'name',
                'multiple' => true
        ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user', 'associated_property')
            ->add('module', 'associated_property')
        ;
    }

    public function toString($object)
    {
        return 'UserModule'; // shown in the breadcrumb on the create view        
    }
}