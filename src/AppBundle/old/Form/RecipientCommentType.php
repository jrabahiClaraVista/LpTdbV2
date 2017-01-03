<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RecipientCommentType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->remove('contactedAt')
      ->remove('createdAt')
      ->remove('modifiedAt')
      ->remove('campaign')
      ->remove('client')
      ->remove('channel')
      ->remove('optIn')
      ->remove('optOutOther')
      ->remove('optOutNotRelevant')
      ->add('submit', 'submit')
    ;
  }

  public function getName()
  {
    return 'appbundle_recipient_comment';
  }

  public function getParent()
  {
    return new RecipientType();
  }
}