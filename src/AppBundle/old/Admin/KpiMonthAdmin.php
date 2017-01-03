<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class KpiMonthAdmin extends Admin
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
            ->add('nb_transac_m0', 'integer')
            ->add('tx_transac_npe_m0', 'integer')
            ->add('tx_transac_nve_m0', 'integer')
            ->add('tx_transac_npes_m0', 'integer')
            ->add('tx_transac_nves_m0', 'integer')
            ->add('tx_transac_npesa_m0', 'integer')
            ->add('tx_transac_nvesa_m0', 'integer')
            ->add('top_npe_m0', 'text')
            ->add('taux_top_npe_m0', 'integer')
            ->add('top_npes_m0', 'text')
            ->add('taux_top_npes_m0', 'integer')
            ->add('top_npesa_m0', 'text')
            ->add('taux_top_npesa_m0', 'integer')
            ->add('nbre_clients_contactables_email_h', 'integer')
            ->add('nbre_clients_inactifs_email_h', 'integer')
            ->add('nbre_clients_animes_m0', 'integer')
            ->add('nbre_clients_transformes_m0', 'integer')
            ->add('CA_clients_transformes_m0', 'integer')
            ->add('top3_boutique_1_m0', 'text')
            ->add('top3_boutique_1_CA_m0', 'integer')
            ->add('top3_boutique_2_m0', 'text')
            ->add('top3_boutique_2_CA_m0', 'integer')
            ->add('top3_boutique_3_m0', 'text')
            ->add('top3_boutique_3_CA_m0', 'integer')
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
            ->add('nb_transac_m0', 'integer')
            ->add('tx_transac_npe_m0', 'integer')
            ->add('tx_transac_nve_m0', 'integer')
            ->add('tx_transac_npes_m0', 'integer')
            ->add('tx_transac_nves_m0', 'integer')
            ->add('tx_transac_npesa_m0', 'integer')
            ->add('tx_transac_nvesa_m0', 'integer')
            ->add('nbre_clients_contactables_email_h', 'integer')
            ->add('nbre_clients_inactifs_email_h', 'integer')
            ->add('nbre_clients_animes_m0', 'integer')
            ->add('nbre_clients_transformes_m0', 'integer')
            ->add('CA_clients_transformes_m0', 'integer')
        ;
    }

    public function toString($object)
    {
        return 'KpiMonth'; // shown in the breadcrumb on the create view        
    }
}