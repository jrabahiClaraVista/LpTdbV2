<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\ImportFile;

class ImportFileLogService
{

  public function __construct(EntityManager $entityManager)
  {
    $this->em = $entityManager;
  }

  public function AddImportFile($result)
  {

    $importFile = new ImportFile();
    $importFile->setName($result->getName());
    $importFile->setStartTime($result->getStartTime());
    $importFile->setEndTime($result->getEndTime());
    $importFile->setErrorCount($result->getErrorCount());
    $importFile->setSuccessCount($result->getSuccessCount());
    $importFile->setTotalProcessedCount($result->getTotalProcessedCount());
    $importFile->setHasErrors($result->hasErrors());

    $this->em->persist($importFile);

    $this->em->flush();

  }

}