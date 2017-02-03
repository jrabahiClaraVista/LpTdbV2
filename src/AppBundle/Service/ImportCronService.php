<?php
// src/OC/PlatformBundle/Antispam/OCAntispam.php

namespace AppBundle\Service;

use Application\Sonata\HelperBundle\Helper\CSVTypes;

use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\DoctrineWriter;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Client;
use AppBundle\Entity\Recipient;
use AppBundle\Entity\Import;
use Application\Sonata\UserBundle\Entity\User;

class ImportCronService
{

	private $separator;

	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

    public function setSeparator($separator) 
    {
        $this->separator = $separator;
    }

    public function renameLastImport() 
    {   
        $date = new \DateTime();
        $date = $date->format("Ym");
        rename ("/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/TABLEAU_DE_BORD_lp_rq.csv" , "/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/archives/TABLEAU_DE_BORD_lp_rq_".$date.".csv" );
    }

    //////////////////////////////////////////
    #client Lp
	public function importClientCSVFileLp($curEntityClass)
    {        
        $filename = '/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/TABLEAU_DE_BORD_lp_rq.csv';
        $file = new \SplFileObject($filename);
        if ($file === false) die("Can't open filestream $filename");

        // Create and configure the reader
        $csvReader = new CsvReader($file, $this->separator);
        if ($csvReader===false) die("Can't create csvReader $filename");
        $csvReader->setHeaderRowNumber(0);

        // this must be done to import CSVs where one of the data-field has CRs within!
        $file->setFlags(\SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD);

        // Set Database into "nonchecking Foreign Keys"
        //$this->em=$this->getDoctrine()->getManager();
        $this->em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=0;");

        $date = new \DateTime();
        $date = $date->format('mdYHms');
        // Create the workflow
        $workflow = new Workflow($csvReader, null, "cron_import_".$date );
        if ($workflow===false) die("Can't create workflow $filename");
        
        
        switch ($curEntityClass) {
            case 'KpiMonth':
                $curEntityClass = CSVTypes::getEntityClass(CSVTypes::KpiMonth);
                break;
            case 'KpiYearToDate':
                $curEntityClass = CSVTypes::getEntityClass(CSVTypes::KpiYearToDate);
                break;
            default:
                $curEntityClass = CSVTypes::getEntityClass(CSVTypes::User);
                break;
        }        
        
        // parameters in array to find existing lines in BDD and update id found depending on $index

        $headers = $csvReader->getColumnHeaders();
        $csvReader->setColumnHeaders(['username','email','role','dr','brand','date','nbTransacM0','nbTransacYtd','txTransacNpeM0','txTransacNveM0','txTransacNpeYtd','txTransacNveYtd','txTransacNpesM0','txTransacNvesM0','txTransacNpesYtd','txTransacNvesYtd','txTransacNpesaM0','txTransacNvesaM0','txTransacNpesaYtd','txTransacNvesaYtd','rankNpeM0','rankNpeYtd','rankNpesM0','rankNpesYtd','rankNpesaM0','rankNpesaYtd','nbreClientsContactablesEmailH','nbreClientsInactifsEmailH','nbreClientsAnimesM0','nbreClientsTransformesM0','caClientsTransformesM0','caCrmYtd']);

        #Lp writers
        switch ($curEntityClass) {
            case CSVTypes::getEntityClass(CSVTypes::KpiMonth):
                    $writer = new DoctrineWriter($this->em, $curEntityClass);                     
                break;
            case CSVTypes::getEntityClass(CSVTypes::KpiYearToDate):
                    $writer = new DoctrineWriter($this->em, $curEntityClass);  
                break;
            default:
                    $writer = new DoctrineWriter($this->em, $curEntityClass, array('username'));  
                break;
        }        

        $writer->setTruncate(false);

        $entityMetadata=$this->em->getClassMetadata($curEntityClass);
        $entityMetadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $workflow->addWriter($writer);

        /////////////////////////////////////
        $result = $workflow->process();

        // RESetting Database Check Status        
        $this->em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=1;");



        return $result;
    }

    #Update User's Kpis field
    public function setUserforKpiLp(){
        $kpiMonths = $this->em->getRepository('AppBundle:KpiMonth')->findBy(
            array('user' => null)
        );
        $kpiYearToDates = $this->em->getRepository('AppBundle:KpiYearToDate')->findBy(
            array('user' => null)
        );

        foreach ($kpiMonths as $key => $kpiMonth) {
            if($kpiMonth->getUsername() != "" && $kpiMonth->getUsername() != null){
                $user = $this->em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(
                    array('username' => $kpiMonth->getUsername())
                );
                $kpiMonth->setUser($user);
            }
        }

        foreach ($kpiYearToDates as $key => $kpiYearToDate) {
            if($kpiMonth->getUsername() != "" && $kpiMonth->getUsername() != null){
                $user = $this->em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(
                    array('username' => $kpiYearToDate->getUsername())
                );
                $kpiYearToDate->setUser($user);
            }
        }

        $this->em->flush();
    }

    public function updateRoles() {
         $users = $this->em->getRepository('ApplicationSonataUserBundle:User')->findAll();

         foreach ($users as $key => $user) {
            if ( !$user->hasRole( $user->getRole() ) ) {
                $user->addRole($user->getRole()) ;
            }
        }

        $this->em->flush();

    }

    //////////////////////////////////////////
    #client Lncl
    public function importClientCSVFileLncl()
    {        

        $filename = 'D:\wamp\www\StoreApp\web\imports\lancel_fichier_test_outil_clienteling_TAA.csv';
        $file = new \SplFileObject($filename);
        if ($file === false) die("Can't open filestream $filename");

        // Create and configure the reader
        $csvReader = new CsvReader($file, $this->separator);
        if ($csvReader===false) die("Can't create csvReader $filename");
        $csvReader->setHeaderRowNumber(0);

        // this must be done to import CSVs where one of the data-field has CRs within!
        $file->setFlags(\SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD);

        // Set Database into "nonchecking Foreign Keys"
        //$this->em=$this->getDoctrine()->getManager();
        $this->em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=0;");

        $date = new \DateTime();
        $date = $date->format('mdYHms');
        // Create the workflow
        $workflow = new Workflow($csvReader, null, "cron_import_".$date );
        if ($workflow===false) die("Can't create workflow $filename");
        
        $curEntityClass = CSVTypes::getEntityClass(CSVTypes::Client);
        
        // parameters in array to find existing lines in BDD and update id found depending on $index

        $headers = $csvReader->getColumnHeaders();
        $csvReader->setColumnHeaders(['idClient','idcampagne','campaignEntryAt','channel','lastname','firstname','store','email','phone1','phone2','country','city','postalCode','adress1','adress2','adress3','nationality','ca3Years','ca12Months','frequence3Years','frequence12Months','maxPrice3Years','maxPrice12Months','segment','codeUvc','skuDesc','genreDesc','ligneDesc','pricePaid','empty1','empty2','empty3','empty4','empty5','empty6','empty7','empty8','empty9','empty10']);

        #le champ id_client devra être créer plus tard et sera utilisé pour faire le lien avec le datamart
        #Gestion pour les dirrentes entités ici : args = $entityManager, $entityName, $index = null
        
        #Lncl $writer
        $writer = new DoctrineWriter($this->em, $curEntityClass, array('idClient'));
        $writerTwo = new DoctrineWriter($this->em, CSVTypes::getEntityClass(CSVTypes::Import), array('idClient'));

        $writer->setTruncate(false);

        $entityMetadata=$this->em->getClassMetadata($curEntityClass);
        $entityMetadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $workflow->addWriter($writer);
        $workflow->addWriter($writerTwo);

        $result = $workflow->process();

        // RESetting Database Check Status        
        $this->em->getConnection()->exec("SET FOREIGN_KEY_CHECKS=1;");

        // After successfully import, some files need special treatment --> Reset some DB fields
        $recipients = array();

        //If client imported
        
        //Get all Clients
        $clients = $this->em->getRepository('AppBundle:Client')->getClientsWithCampaignId();

        //For each Client
        foreach ($clients as $key => $client) { 
            //Get Campaign that match with campaignId
            $campaign = $this->em->getRepository('AppBundle:Campaign')->findOneBy(array( 'idCampaignName' =>  $client->getIdCampaignName() ));          

            //If Campaign IS NOT NULL
            if($campaign) {
                //Get Recipient
                $recipient = $this->em->getRepository('AppBundle:Recipient')->findOneBy(array('campaign' => $campaign, 'client' => $client));
                
                //If Recipient DO NOT EXIST
                if ($recipient == null){
                    //CREATE Recipient
                    $recipients[$key] = new Recipient();

                    $recipients[$key]->setCampaign($campaign);
                    $recipients[$key]->setClient($client);

                    $this->em->persist( $recipients[$key] );
                }
            }
        }
        $this->em->flush();
        $this->em->clear();

        return $result;
    }


    public function deleteClientsNotInImportLncl()
    {

        $imports = $this->em->getRepository('AppBundle:Import')->findAll();
        $clients = $this->em->getRepository('AppBundle:Client')->findAll();

        $newImports = array();

        foreach ($imports as $key => $import) {
            $newImports[$key] = $import->getIdClient();
        }
        foreach ($clients as $key => $client) {
            if (! in_array($client->getIdClient(), $newImports)) {
                $this->em->remove($client);
            }
        }

        $this->em->flush();
    }


    public function deleteImportLncl()
    {

        $imports = $this->em->getRepository('AppBundle:Import')->findAll();

        foreach ($imports as $key => $import) {
            $deletes[$key] = $import;
            $this->em->remove( $deletes[$key] );
        }
        $this->em->flush();

    }

}