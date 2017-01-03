<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CampaignKpiType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->remove('description')
      ->remove('startDate')
      ->remove('endDate')
    ;
  }

  public function getName()
  {
    return 'appbundle_campaign_kpi';
  }

  public function getParent()
  {
    return new CampaignType();
  }
}