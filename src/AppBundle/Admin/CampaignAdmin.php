<?php
// src/AppBundle/Admin/Campaign.php
namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use AppBundle\Form\ImageType;

class CampaignAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab("General informations")
                ->with("Content")
                    ->add('name', 'text')
                    ->add('brand', 'text', array('required' => false, 'label' => "Brand (Uppercase)"))
                    ->add('idCampaignName', 'text', array('required' => false, 'label' => "Campaign Id (for trigger)"))
                    ->add('description', 'textarea', array('required' => false))
                    ->add('activeCampaign', 'checkbox', array('required' => false, 'label' => "Active for Module Campaign"))
                    ->add('activeKpi', 'checkbox', array('required' => false, 'label' => "Active for Module Kpi"))
                ->end()
                ->with("Kpis Module")
                    ->add( 'image', new ImageType(), array('required' => false,'label' => 'Image') )
                    ->add('emailsSent', 'integer', array('required' => false))
                    ->add('smsSent', 'integer', array('required' => false))
                    ->add('mailsSent', 'integer', array('required' => false))
                    ->add('txO', 'number', array('required' => false))
                    ->add('txC', 'number', array('required' => false))
                    ->add('ca', 'integer', array('required' => false))
                ->end()
                ->with("Dates")
                    ->add('startDate', 'date')
                    ->add('endDate', 'date')
                ->end()
                ->with('Channels priority')
                    ->add('channelOne', 'choice', array(
                        'choices' => array(
                            null    => 'Select a channel',
                            'Email' => 'Email',
                            'Mail'  => 'Mail',
                            'Phone' => 'Phone',
                            'SMS'   => 'SMS',
                            ),
                        'required' => false,
                        'label' => 'Priority 1'
                        )
                    )
                    ->add('channelTwo', 'choice', array(
                        'choices' => array(
                            null    => 'Select a channel',
                            'Email' => 'Email',
                            'Mail'  => 'Mail',
                            'Phone' => 'Phone',
                            'SMS'   => 'SMS',
                            ),
                        'required' => false,
                        'label' => 'Priority 2'
                        )
                    )
                    ->add('channelThree', 'choice', array(
                        'choices' => array(
                            null    => 'Select a channel',
                            'Email' => 'Email',
                            'Mail'  => 'Mail',
                            'Phone' => 'Phone',
                            'SMS'   => 'SMS',
                            ),
                        'required' => false,
                        'label' => 'Priority 3'
                        )
                    )
                    ->add('channelFour', 'choice', array(
                        'choices' => array(
                            null    => 'Select a channel',
                            'Email' => 'Email',
                            'Mail'  => 'Mail',
                            'Phone' => 'Phone',
                            'SMS'   => 'SMS',
                            ),
                        'required' => false,
                        'label' => 'Priority 4'
                        )
                    )
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
            ->add('description')
            ->add('channelOne')
            //->add('campaignChannels', 'associated_property')
            ->add('emailsSent')
            ->add('mailsSent')
            ->add('smsSent')
            ->add('state')
            ->add('image.url')
        ;
    }

    public function toString($object)
    {
        return 'Campaign' . $object->getName() ; // shown in the breadcrumb on the create view        
    }
}