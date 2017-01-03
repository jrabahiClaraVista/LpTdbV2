<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Client;

class DeleteClientDailyCronService
{

  public function __construct(EntityManager $entityManager)
  {
    $this->em = $entityManager;
  }

  public function deleteJPlus15Clients()
  {
    
    $clients = $this->em->getRepository('AppBundle:Client')->findAll();

    $deleted = 0;

    foreach ($clients as $key => $client) {
      $date1 = $client->getCampaignEntryAt();
      $date2 = new \DateTime();

      $dateDiff =  $date1->diff($date2);

      $diffs[$key] = $dateDiff->format("%R%a");

      if( ($diffs[$key] - 15) > 0 ){
        $this->em->remove($clients[$key]);
        $deleted++;
      }
    }

    $this->em->flush();

    return $deleted ;

  }

}
