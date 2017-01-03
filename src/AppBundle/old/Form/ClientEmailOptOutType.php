<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ClientEmailOptOutType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
            ->remove('idClient')
            ->remove('firstname')
            ->remove('lastname')
            ->remove('email')
            ->remove('phone1')
            ->remove('phone2')
            ->remove('adress1')
            ->remove('adress2')
            ->remove('ville')
            ->remove('codePostal')
            ->remove('country')
            ->remove('birthday')
            ->remove('lastContact')
            ->remove('phoneErr')
            ->remove('adressErr')
            ->remove('optOutMail')
            ->remove('optOutPhone')
            ->remove('optOutSms')
            ->remove('score')
            ->remove('createdAt')
            ->remove('modifiedAt')
            ->remove('active')
            ->remove('vendorCode')
            //->add('emailErr')
            //->add('optOutEmail')
    ;
  }

  public function getName()
  {
    return 'appbundle_client_email_opt_out';
  }

  public function getParent()
  {
    return new ClientType();
  }
}