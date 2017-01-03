<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ExcludeClientFromCampaignType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->remove('contactedAt')
      ->remove('createdAt')
      ->remove('modifiedAt')
      ->remove('campaign')
      ->remove('client')
      ->remove('optIn')
      ->remove('comment')
      ->remove('channel')
      ->add('client', New ClientEmailOptOutType)
      ->add('submit', 'submit')
    ;
  }

  public function getName()
  {
    return 'appbundle_exclude_client_from_campaign';
  }

  public function getParent()
  {
    return new RecipientType();
  }
}