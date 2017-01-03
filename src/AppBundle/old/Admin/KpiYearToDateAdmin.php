<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class KpiYearToDateAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', 'sonata_type_model', array(
                'class'    => 'Application\Sonata\UserBundle\Entity\User',
                'property' => 'username'
                )
            )
            ->add('date', 'date')
            ->add('nb_transac_ytd', 'integer')
            ->add('tx_transac_npe_ytd', 'integer')
            ->add('tx_transac_nve_ytd', 'integer')
            ->add('tx_transac_npes_ytd', 'integer')
            ->add('tx_transac_nves_ytd', 'integer')
            ->add('tx_transac_npesa_ytd', 'integer')
            ->add('tx_transac_nvesa_ytd', 'integer')
            ->add('top_npe_ytd', 'text')
            ->add('taux_top_npe_ytd', 'integer')
            ->add('top_npes_ytd', 'text')
            ->add('taux_top_npes_ytd', 'integer')
            ->add('top_npesa_ytd', 'text')
            ->add('taux_top_npesa_ytd', 'integer')
            ->add('ca_crm_ytd', 'integer')
            ->add('top3_boutique_1_ytd', 'text')
            ->add('top3_boutique_1_email_ytd', 'integer')
            ->add('top3_boutique_2_ytd', 'text')
            ->add('top3_boutique_2_email_ytd', 'integer')
            ->add('top3_boutique_3_ytd', 'text')
            ->add('top3_boutique_3_email_ytd', 'text')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user', 'associated_property')
            ->add('date', 'date')
            ->add('nb_transac_ytd', 'integer')
            ->add('tx_transac_npe_ytd', 'integer')
            ->add('tx_transac_nve_ytd', 'integer')
            ->add('tx_transac_npes_ytd', 'integer')
            ->add('tx_transac_nves_ytd', 'integer')
            ->add('tx_transac_npesa_ytd', 'integer')
            ->add('tx_transac_nvesa_ytd', 'integer')
            ->add('ca_crm_ytd', 'integer')
        ;
    }

    public function toString($object)
    {
        return 'KpiYearToDate'; // shown in the breadcrumb on the create view        
    }
}