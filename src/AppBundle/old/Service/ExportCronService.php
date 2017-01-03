<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Client;
use AppBundle\Entity\Recipient;

//use Ijanki\Bundle\FtpBundle\Exception\FtpException;

class ExportCronService
{

  public function __construct(EntityManager $entityManager)
  {
    $this->em = $entityManager;
  }

  public function createExportClientCSVFile()
  {
    
    @unlink('D:\wamp\www\StoreApp\web\export\export.csv');
    $results = $this->em->getRepository('AppBundle:Client')->findAll();

    //$results = array_pop($results);

    $handle = fopen('D:\wamp\www\StoreApp\web\export\export.csv', 'w+');
    // Nom des colonnes du CSV 
    fputcsv($handle, array('ID CLient',
      'firstname',
      'lastname'
      ),';');

    //Champs
    foreach( $results as $row ) 
    {
      
      fputcsv($handle,array($row->getIdClient(),
        $row->getFirstname(),
        $row->getLastname()
        ),'|');
    }

    fclose($handle);

  }

  /*public function exportCSVFileToFtp($host, $username, $password, $sourceFile, $destinationFile)
  {
    try {
        $ftp = $this->container->get('ijanki_ftp');
        $ftp->connect($host);
        $ftp->login($username, $password);
        $ftp->put($destination_file, $source_file, FTP_BINARY);

    } catch (FtpException $e) {
        echo 'Error: ', $e->getMessage();
    }
  }*/
}
