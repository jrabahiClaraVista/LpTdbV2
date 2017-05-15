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

    public function importKpiCaptureCSVFile( InputInterface $input, OutputInterface $output, $csv = null)
    {        
        $date = new \DateTime();
        $date = $date->format("Ymd");

        if($csv == null)
        {
            if($this->ip == "127.0.0.1")
            {
                $file = fopen("D:\\wamp\\www\\StoreApp\\web\\imports\\kpis\\TABLEAU_DE_BORD_lp_rq.csv", "r");
            }
            else{
                $file = fopen("/data/ftp/imports/kpis/TABLEAU_DE_BORD_lp_rq.csv", "r");
            }
        }
        else
        {
            if($this->ip == "127.0.0.1")
            {
                $file = fopen("D:\\wamp\\www\\StoreApp\\web\\imports\\kpis\\".$csv."", "r");
            }
            else{
                $file = fopen("/data/ftp/imports/kpis/".$csv."", "r");
            }  
        }

               
        $header = "user_id,code_boutique_vendeur,point_vente_desc,niveau,date,nb_cli_m_l,pct_cli_coord_valid_m_l,pct_cli_coord_nonvalid_m_l,pct_cli_coord_nonrens_m_l,pct_cli_email_valid_m_l,pct_cli_email_nonvalid_m_l,pct_cli_email_nonrens_m_l,pct_cli_tel_valid_m_l,pct_cli_tel_nonvalid_m_l,pct_cli_tel_nonrens_m_l,pct_cli_add_valid_m_l,pct_cli_add_nonvalid_m_l,pct_cli_add_nonrens_m_l,nb_cli_actifs_m_l,nb_cli_actifs_new_m_l,nb_cli_actifs_exist_m_l,nb_prosp_m_l,pct_prosp_coord_valid_m_l,pct_prosp_coord_nonvalid_m_l,pct_prosp_coord_nonrens_m_l,pct_prosp_email_valid_m_l,pct_prosp_email_nonvalid_m_l,pct_prosp_email_nonrens_m_l,pct_prosp_tel_valid_m_l,pct_prosp_tel_nonvalid_m_l,pct_prosp_tel_nonrens_m_l,pct_prosp_add_valid_m_l,pct_prosp_add_nonvalid_m_l,pct_prosp_add_nonrens_m_l,nb_cli_m_nl,pct_cli_coord_valid_m_nl,pct_cli_coord_nonvalid_m_nl,pct_cli_coord_nonrens_m_nl,pct_cli_email_valid_m_nl,pct_cli_email_nonvalid_m_nl,pct_cli_email_nonrens_m_nl,pct_cli_tel_valid_m_nl,pct_cli_tel_nonvalid_m_nl,pct_cli_tel_nonrens_m_nl,pct_cli_add_valid_m_nl,pct_cli_add_nonvalid_m_nl,pct_cli_add_nonrens_m_nl,nb_cli_actifs_m_nl,nb_cli_actifs_new_m_nl,nb_cli_actifs_exist_m_nl,nb_prosp_m_nl,pct_prosp_coord_valid_m_nl,pct_prosp_coord_nonvalid_m_nl,pct_prosp_coord_nonrens_m_nl,pct_prosp_email_valid_m_nl,pct_prosp_email_nonvalid_m_nl,pct_prosp_email_nonrens_m_nl,pct_prosp_tel_valid_m_nl,pct_prosp_tel_nonvalid_m_nl,pct_prosp_tel_nonrens_m_nl,pct_prosp_add_valid_m_nl,pct_prosp_add_nonvalid_m_nl,pct_prosp_add_nonrens_m_nl,nb_cli_y_l,pct_cli_coord_valid_y_l,pct_cli_coord_nonvalid_y_l,pct_cli_coord_nonrens_y_l,pct_cli_email_valid_y_l,pct_cli_email_nonvalid_y_l,pct_cli_email_nonrens_y_l,pct_cli_tel_valid_y_l,pct_cli_tel_nonvalid_y_l,pct_cli_tel_nonrens_y_l,pct_cli_add_valid_y_l,pct_cli_add_nonvalid_y_l,pct_cli_add_nonrens_y_l,nb_email_y_l,nb_tel_y_l,nb_adr_y_l,nb_cli_actifs_y_l,nb_cli_actifs_new_y_l,nb_cli_actifs_exist_y_l,pct_cli_donnees_nonvalid_y_l,nb_email_nonvalid_y_l,nb_tel_nonvalid_y_l,nb_adr_nonvalid_y_l,nb_prosp_y_l,pct_prosp_coord_valid_y_l,pct_prosp_coord_nonvalid_y_l,pct_prosp_coord_nonrens_y_l,pct_prosp_email_valid_y_l,pct_prosp_email_nonvalid_y_l,pct_prosp_email_nonrens_y_l,pct_prosp_tel_valid_y_l,pct_prosp_tel_nonvalid_y_l,pct_prosp_tel_nonrens_y_l,pct_prosp_add_valid_y_l,pct_prosp_add_nonvalid_y_l,pct_prosp_add_nonrens_y_l,nb_cli_y_nl,pct_cli_coord_valid_y_nl,pct_cli_coord_nonvalid_y_nl,pct_cli_coord_nonrens_y_nl,pct_cli_email_valid_y_nl,pct_cli_email_nonvalid_y_nl,pct_cli_email_nonrens_y_nl,pct_cli_tel_valid_y_nl,pct_cli_tel_nonvalid_y_nl,pct_cli_tel_nonrens_y_nl,pct_cli_add_valid_y_nl,pct_cli_add_nonvalid_y_nl,pct_cli_add_nonrens_y_nl,nb_email_y_nl,nb_tel_y_nl,nb_adr_y_nl,nb_cli_actifs_y_nl,nb_cli_actifs_new_y_nl,nb_cli_actifs_exist_y_nl,pct_cli_donnees_nonvalid_y_nl,nb_email_nonvalid_y_nl,nb_tel_nonvalid_y_nl,nb_adr_nonvalid_y_nl,nb_prosp_y_nl,pct_prosp_coord_valid_y_nl,pct_prosp_coord_nonvalid_y_nl,pct_prosp_coord_nonrens_y_nl,pct_prosp_email_valid_y_nl,pct_prosp_email_nonvalid_y_nl,pct_prosp_email_nonrens_y_nl,pct_prosp_tel_valid_y_nl,pct_prosp_tel_nonvalid_y_nl,pct_prosp_tel_nonrens_y_nl,pct_prosp_add_valid_y_nl,pct_prosp_add_nonvalid_y_nl,pct_prosp_add_nonrens_y_nl,nb_trans_linked_y,nb_trans_local_y,pct_trans_local_y,nb_trans_nlocal_y,pct_trans_nlocal_y,nb_trans_not_linked_y,pct_trans_not_linked_y,nb_trans_tot_y,nb_trans_linked_m,nb_trans_local_m,pct_trans_local_m,nb_trans_nlocal_m,pct_trans_nlocal_m,nb_trans_not_linked_m,pct_trans_not_linked_m,nb_trans_tot_m,nb_optin_y_l,nb_optout_y_l,pct_optin_y_l,pct_optout_y_l,nb_cli_coord_valid_y_l,nb_cli_coord_nonvalid_y_l,nb_cli_coord_nonrens_y_l,nb_cli_email_valid_y_l,nb_cli_email_nonvalid_y_l,nb_cli_email_nonrens_y_l,nb_cli_tel_valid_y_l,nb_cli_tel_nonvalid_y_l,nb_cli_tel_nonrens_y_l,nb_cli_add_valid_y_l,nb_cli_add_nonvalid_y_l,nb_cli_add_nonrens_y_l,nb_prosp_coord_valid_y_l,nb_prosp_coord_nonvalid_y_l,nb_prosp_coord_nonrens_y_l,nb_prosp_email_valid_y_l,nb_prosp_email_nonvalid_y_l,nb_prosp_email_nonrens_y_l,nb_prosp_tel_valid_y_l,nb_prosp_tel_nonvalid_y_l,nb_prosp_tel_nonrens_y_l,nb_prosp_add_valid_y_l,nb_prosp_add_nonvalid_y_l,nb_prosp_add_nonrens_y_l,nb_optin_y_nl,nb_optout_y_nl,pct_optin_y_nl,pct_optout_y_nl,nb_cli_coord_valid_y_nl,nb_cli_coord_nonvalid_y_nl,nb_cli_coord_nonrens_y_nl,nb_cli_email_valid_y_nl,nb_cli_email_nonvalid_y_nl,nb_cli_email_nonrens_y_nl,nb_cli_tel_valid_y_nl,nb_cli_tel_nonvalid_y_nl,nb_cli_tel_nonrens_y_nl,nb_cli_add_valid_y_nl,nb_cli_add_nonvalid_y_nl,nb_cli_add_nonrens_y_nl,nb_prosp_coord_valid_y_nl,nb_prosp_coord_nonvalid_y_nl,nb_prosp_coord_nonrens_y_nl,nb_prosp_email_valid_y_nl,nb_prosp_email_nonvalid_y_nl,nb_prosp_email_nonrens_y_nl,nb_prosp_tel_valid_y_nl,nb_prosp_tel_nonvalid_y_nl,nb_prosp_tel_nonrens_y_nl,nb_prosp_add_valid_y_nl,nb_prosp_add_nonvalid_y_nl,nb_prosp_add_nonrens_y_nl,nb_optin_m_l,nb_optout_m_l,pct_optin_m_l,pct_optout_m_l,nb_cli_coord_valid_m_l,nb_cli_coord_nonvalid_m_l,nb_cli_coord_nonrens_m_l,nb_cli_email_valid_m_l,nb_cli_email_nonvalid_m_l,nb_cli_email_nonrens_m_l,nb_cli_tel_valid_m_l,nb_cli_tel_nonvalid_m_l,nb_cli_tel_nonrens_m_l,nb_cli_add_valid_m_l,nb_cli_add_nonvalid_m_l,nb_cli_add_nonrens_m_l,nb_prosp_coord_valid_m_l,nb_prosp_coord_nonvalid_m_l,nb_prosp_coord_nonrens_m_l,nb_prosp_email_valid_m_l,nb_prosp_email_nonvalid_m_l,nb_prosp_email_nonrens_m_l,nb_prosp_tel_valid_m_l,nb_prosp_tel_nonvalid_m_l,nb_prosp_tel_nonrens_m_l,nb_prosp_add_valid_m_l,nb_prosp_add_nonvalid_m_l,nb_prosp_add_nonrens_m_l,nb_optin_m_nl,nb_optout_m_nl,pct_optin_m_nl,pct_optout_m_nl,nb_cli_coord_valid_m_nl,nb_cli_coord_nonvalid_m_nl,nb_cli_coord_nonrens_m_nl,nb_cli_email_valid_m_nl,nb_cli_email_nonvalid_m_nl,nb_cli_email_nonrens_m_nl,nb_cli_tel_valid_m_nl,nb_cli_tel_nonvalid_m_nl,nb_cli_tel_nonrens_m_nl,nb_cli_add_valid_m_nl,nb_cli_add_nonvalid_m_nl,nb_cli_add_nonrens_m_nl,nb_prosp_coord_valid_m_nl,nb_prosp_coord_nonvalid_m_nl,nb_prosp_coord_nonrens_m_nl,nb_prosp_email_valid_m_nl,nb_prosp_email_nonvalid_m_nl,nb_prosp_email_nonrens_m_nl,nb_prosp_tel_valid_m_nl,nb_prosp_tel_nonvalid_m_nl,nb_prosp_tel_nonrens_m_nl,nb_prosp_add_valid_m_nl,nb_prosp_add_nonvalid_m_nl,nb_prosp_add_nonrens_m_nl,nb_prosp_optout_y_l,nb_prosp_optout_y_nl,nb_prosp_optout_m_l,nb_prosp_optout_m_nl,pct_prosp_optout_y_l,pct_prosp_optout_y_nl,pct_prosp_optout_m_l,pct_prosp_optout_m_nl";
        
        //valeurs de la requête (correspond au header du fichier)
        $values = ":".str_replace(",", ",:", $header);
        $values = str_replace(":user_id,", "", $values);
        //tableau des headers à mettre à jours pour la boucle
        $headers = explode(",", str_replace("user_id,", "", $header));
        $update = "";
        $i = 0;
        $len = count($headers);

        foreach ($headers as $key => $value) {
            if ($i == $len - 1) $update .= $value." = :".$value;
            else $update .= $value." = :".$value.",";
            $i++;
        }

        $sql = "INSERT INTO app_kpi_capture ( ".$header." ) VALUES (  (SELECT id from fos_user_user u WHERE u.libelle = :libelle) , ".$values.")
                ON DUPLICATE KEY UPDATE ".$update."
        "; 

        $i = 0;
        $flag = true;

        while( ($csvfilelines = fgetcsv($file, 0, $this->separator)) != FALSE )
        {
            if($flag) { $flag = false; continue; } //ignore first line of csv             
            
            $stmt = $this->pdo->prepare($sql);

            foreach ($headers as $key => $col) {
                $stmt->bindValue(':'.$col, $csvfilelines[$key], \PDO::PARAM_STR);
            }


            $stmt->bindValue(':libelle', $csvfilelines[1], \PDO::PARAM_STR);
            $stmt->execute();

            if($i % 20 == 0){
                $output->writeln($i." lignes importees");
                gc_collect_cycles();
            }
            $i++;
            //die();
        }
        $output->writeln($i." lignes importees");
        
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


}