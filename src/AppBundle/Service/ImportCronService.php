<?php
// src/OC/PlatformBundle/Antispam/OCAntispam.php

namespace AppBundle\Service;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Client;
use AppBundle\Entity\Recipient;
use AppBundle\Entity\Import;
use Application\Sonata\UserBundle\Entity\User;

class ImportCronService
{

    private $separator;
    private $pdo;
    private $ip;
    private $em;
    private $container;
    protected $encoderFactory;

    public function __construct($local_ip, ContainerInterface $container, EntityManager $entityManager)
    {
        $this->em           = $entityManager;
        $this->ip           = $local_ip;
        $this->container    = $container;
        
        $this->encoder = $this->container->get('security.password_encoder');

        $this->pdo = $this->container->get('app.pdo_connect');
        $this->pdo = $this->pdo->initPdoClienteling();
    }

    public function setSeparator($separator) 
    {
        $this->separator = $separator;
    }

    public function scanDir(){

        if($this->ip == "127.0.0.1")
        {
            $this->filesList = scandir("D:\wamp64\www\LpTdbV3\web\imports");
        }
        else{
            $this->filesList = scandir("/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports");
        }

        return $this->filesList;
    }

    public function renameLastImport($name = null) 
    {   
        $date = new \DateTime();
        $dateWeek = new \DateTime();
        $date = $date->format("Ym");
        $dateWeek = $dateWeek->format("YmdW");
        
        if($name != null)
            rename ("/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/TABLEAU_DE_BORD_lp_".$name."_rq.csv" , "/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/archives/TABLEAU_DE_BORD_lp_".$name."_rq_".$date.".csv" );
        else
            rename ("/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/TABLEAU_DE_BORD_lp_rq.csv" , "/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/archives/TABLEAU_DE_BORD_lp_rq_".$date.".csv" );
    }

    public function renameLastImportWeek($name = null) 
    {   
        $date = new \DateTime();
        $dateWeek = new \DateTime();
        $date = $date->format("Ym");
        $dateWeek = $dateWeek->format("YmdW");
        
        if($name != null)
            rename ("/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/TABLEAU_DE_BORD_hebdo_lp_".$name."_rq.csv" , "/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/archives/TABLEAU_DE_BORD_hebdo_lp_".$name."_rq_".$dateWeek.".csv" );
        else
            rename ("/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/TABLEAU_DE_BORD_hebdo_lp_rq.csv" , "/srv/data/web/vhosts/louispion-qualification.fr/htdocs/web/imports/archives/TABLEAU_DE_BORD_hebdo_lp_rq_".$dateWeek.".csv" );
    }

    public function importKpiCaptureCSVFile( InputInterface $input, OutputInterface $output, $csv = null)
    {        
        $date = new \DateTime();
        $date = $date->format("Ymd");

        
        $file = fopen($csv, "r");

        $header1 = "username_canonical,username,prenom_vendeur,nom_vendeur,email,email_canonical,role,boutique,dr,brand,enabled,updated_at";
        $header2 = "date,nb_transac_m0,nb_transac_ytd,tx_transac_linked_m0,tx_transac_linked_ytd,tx_transac_npe_m0,tx_transac_nve_m0,tx_transac_npe_ytd,tx_transac_nve_ytd,tx_transac_npes_m0,tx_transac_nves_m0,tx_transac_npes_ytd,tx_transac_nves_ytd,tx_transac_npesa_m0,tx_transac_nvesa_m0,tx_transac_npesa_ytd,tx_transac_nvesa_ytd,rank_npe_m0,rank_npes_m0,rank_npesa_m0,rank_npe_ytd,rank_npes_ytd,rank_npesa_ytd,nbre_clients_contactables_email_h,nbre_clients_inactifs_email_h,nbre_clients_animes_m0,nbre_clients_transformes_m0,CA_clients_transformes_m0,ca_crm_ytd";

        //valeurs de la requête (correspond au header du fichier)
        $values1 = ":".str_replace(",", ",:", $header1);
        //$values1 = str_replace(":user_id,", "", $values1);
        //tableau des headers à mettre à jours pour la boucle
        $headers = explode(",", str_replace("user_id,", "", $header1));
        $update1 = "";
        $i = 0;
        $len = count($headers);

        foreach ($headers as $key => $value) {
            if ($i == $len - 1) $update1 .= $value." = :".$value;
            else $update1 .= $value." = :".$value.",";
            $i++;
        } 

        //valeurs de la requête (correspond au header du fichier)
        $values2 = ":".str_replace(",", ",:", $header2);
        $values2 = str_replace(":user_id,", "", $values2);
        //tableau des headers à mettre à jours pour la boucle
        $headers = explode(",", str_replace("user_id,", "", $header2));
        $update2 = "";
        $i = 0;
        $len = count($headers);

        foreach ($headers as $key => $value) {
            if ($i == $len - 1) $update2 .= $value." = :".$value;
            else $update2 .= $value." = :".$value.",";
            $i++;
        } 


        $sql1 = "INSERT INTO fos_user_user ( ".$header1.", created_at, salt, password, roles,locked,expired,credentials_expired,ispremium ) VALUES ( ".$values1.", :created_at, :salt, :password, :roles,0,0,0,0 )
                ON DUPLICATE KEY UPDATE ".$update1."
        "; 
        $sql2 = "INSERT INTO app_kpi_month ( user_id, ".$header2." ) VALUES (  (SELECT id from fos_user_user u WHERE u.username = :username) , ".$values2.")
                ON DUPLICATE KEY UPDATE ".$update2."
        ";

        $i = 0;
        $flag = true;

        $date = new \Datetime('now');
        $date= $date->format('Y-m-d H:i:s');

        //$user = new User;
        //$this->encoder->encodePassword('Claravista123!', $salt)

        while( ($csvfilelines = fgetcsv($file, 0, $this->separator)) != FALSE )
        {
            if($flag) { $flag = false; continue; } //ignore first line of csv             
            
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt2 = $this->pdo->prepare($sql2);

            $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

            $stmt1->bindValue(':username', $csvfilelines[0], \PDO::PARAM_STR);
            $stmt1->bindValue(':username_canonical', $csvfilelines[0], \PDO::PARAM_STR);
            $stmt1->bindValue(':prenom_vendeur', $csvfilelines[1], \PDO::PARAM_STR);
            $stmt1->bindValue(':nom_vendeur', $csvfilelines[2], \PDO::PARAM_STR);
            $stmt1->bindValue(':email', $csvfilelines[3], \PDO::PARAM_STR);
            $stmt1->bindValue(':email_canonical', $csvfilelines[3], \PDO::PARAM_STR);
            $stmt1->bindValue(':role', $csvfilelines[4], \PDO::PARAM_STR);
            $stmt1->bindValue(':boutique', $csvfilelines[5], \PDO::PARAM_STR);
            $stmt1->bindValue(':dr', $csvfilelines[6], \PDO::PARAM_STR);
            $stmt1->bindValue(':brand', $csvfilelines[7], \PDO::PARAM_STR);
            $stmt1->bindValue(':enabled', 1, \PDO::PARAM_INT);
            $stmt1->bindValue(':created_at', $date, \PDO::PARAM_STR);
            $stmt1->bindValue(':updated_at', $date, \PDO::PARAM_STR);
            $stmt1->bindValue(':salt', $salt, \PDO::PARAM_STR);
            $stmt1->bindValue(':password', "to_change", \PDO::PARAM_STR);
            $stmt1->bindValue(':roles', "a:0:{}", \PDO::PARAM_STR);
            

            $stmt2->bindValue(':username', $csvfilelines[0], \PDO::PARAM_STR);
            $stmt2->bindValue(':date', $csvfilelines[8], \PDO::PARAM_STR);
            $stmt2->bindValue(':nb_transac_m0', $csvfilelines[9], \PDO::PARAM_STR);
            $stmt2->bindValue(':nb_transac_ytd', $csvfilelines[10], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_linked_m0', $csvfilelines[11], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_linked_ytd', $csvfilelines[12], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npe_m0', $csvfilelines[13], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nve_m0', $csvfilelines[14], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npe_ytd', $csvfilelines[15], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nve_ytd', $csvfilelines[16], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npes_m0', $csvfilelines[17], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nves_m0', $csvfilelines[18], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npes_ytd', $csvfilelines[19], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nves_ytd', $csvfilelines[20], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npesa_m0', $csvfilelines[21], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nvesa_m0', $csvfilelines[22], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npesa_ytd', $csvfilelines[23], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nvesa_ytd', $csvfilelines[24], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npe_m0', $csvfilelines[25], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npe_ytd', $csvfilelines[26], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npes_m0', $csvfilelines[27], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npes_ytd', $csvfilelines[28], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npesa_m0', $csvfilelines[29], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npesa_ytd', $csvfilelines[30], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_clients_contactables_email_h', $csvfilelines[31], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_clients_inactifs_email_h', $csvfilelines[32], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_clients_animes_m0', $csvfilelines[33], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_clients_transformes_m0', $csvfilelines[34], \PDO::PARAM_STR);
            $stmt2->bindValue(':CA_clients_transformes_m0', $csvfilelines[35], \PDO::PARAM_STR);
            $stmt2->bindValue(':ca_crm_ytd', $csvfilelines[36], \PDO::PARAM_STR);
            

            try
            {
                $stmt1->execute();
            }
            catch(Exception $e)
            {       
                $output->writeln($e->getMessage());
                die('Erreur 1 : '.$e->getMessage());
            }

            try
            {
                $stmt2->execute();
            }
            catch(Exception $e)
            {       
                $output->writeln($e->getMessage());
                die('Erreur 2 : '.$e->getMessage());
            }

            if($i % 20 == 0){
                $output->writeln($i." lignes importees");
                gc_collect_cycles();
            }
            $i++;
        }
        $output->writeln($i." lignes importees");
        
    }

    public function importKpiCaptureSemaineCSVFile( InputInterface $input, OutputInterface $output, $csv = null)
    {        
        $date = new \DateTime();
        $date = $date->format("Ymd");

        
        $file = fopen($csv, "r");

        $header1 = "username_canonical,username,prenom_vendeur,nom_vendeur,email,email_canonical,role,boutique,dr,brand,enabled,updated_at";
        //$header2 = "date,nb_transac_S0,tx_transac_npe_S0,tx_transac_nve_S0,tx_transac_npes_S0,tx_transac_nves_S0,tx_transac_npesa_S0,tx_transac_nvesa_S0,rank_npe_S0,rank_npes_S0,rank_npesa_S0,nbre_clients_contactables_email_h,nbre_clients_inactifs_email_h,nbre_clients_animes_S0,nbre_clients_transformes_S0,CA_clients_transformes_S0";
        $header2 = "date,nb_transac_S0,tx_transac_linked_S0,tx_transac_npe_S0,tx_transac_nve_S0,tx_transac_npes_S0,tx_transac_nves_S0,tx_transac_npesa_S0,tx_transac_nvesa_S0,rank_npe_S0,rank_npes_S0,rank_npesa_S0";

        //valeurs de la requête (correspond au header du fichier)
        $values1 = ":".str_replace(",", ",:", $header1);
        //$values1 = str_replace(":user_id,", "", $values1);
        //tableau des headers à mettre à jours pour la boucle
        $headers = explode(",", str_replace("user_id,", "", $header1));
        $update1 = "";
        $i = 0;
        $len = count($headers);

        foreach ($headers as $key => $value) {
            if ($i == $len - 1) $update1 .= $value." = :".$value;
            else $update1 .= $value." = :".$value.",";
            $i++;
        } 

        //valeurs de la requête (correspond au header du fichier)
        $values2 = ":".str_replace(",", ",:", $header2);
        $values2 = str_replace(":user_id,", "", $values2);
        //tableau des headers à mettre à jours pour la boucle
        $headers = explode(",", str_replace("user_id,", "", $header2));
        $update2 = "";
        $i = 0;
        $len = count($headers);

        foreach ($headers as $key => $value) {
            if ($i == $len - 1) $update2 .= $value." = :".$value;
            else $update2 .= $value." = :".$value.",";
            $i++;
        } 


        $sql1 = "INSERT INTO fos_user_user ( ".$header1.", created_at, salt, password, roles,locked,expired,credentials_expired,ispremium ) VALUES ( ".$values1.", :created_at, :salt, :password, :roles, 0, 0,0,0 )
                ON DUPLICATE KEY UPDATE ".$update1."
        "; 
        $sql2 = "INSERT INTO app_kpi_week ( user_id, ".$header2." ) VALUES (  (SELECT id from fos_user_user u WHERE u.username = :username) , ".$values2.")
                ON DUPLICATE KEY UPDATE ".$update2."
        ";

        $i = 0;
        $flag = true;

        $date = new \Datetime('now');
        $date= $date->format('Y-m-d H:i:s');

        //$user = new User;
        //$this->encoder->encodePassword('Claravista123!', $salt)

        while( ($csvfilelines = fgetcsv($file, 0, $this->separator)) != FALSE )
        {
            if($flag) { $flag = false; continue; } //ignore first line of csv             
            
            $stmt1 = $this->pdo->prepare($sql1);
            $stmt2 = $this->pdo->prepare($sql2);

            $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

            $stmt1->bindValue(':username', $csvfilelines[0], \PDO::PARAM_STR);
            $stmt1->bindValue(':username_canonical', $csvfilelines[0], \PDO::PARAM_STR);
            $stmt1->bindValue(':prenom_vendeur', $csvfilelines[1], \PDO::PARAM_STR);
            $stmt1->bindValue(':nom_vendeur', $csvfilelines[2], \PDO::PARAM_STR);
            $stmt1->bindValue(':email', $csvfilelines[3], \PDO::PARAM_STR);
            $stmt1->bindValue(':email_canonical', $csvfilelines[3], \PDO::PARAM_STR);
            $stmt1->bindValue(':role', $csvfilelines[4], \PDO::PARAM_STR);
            $stmt1->bindValue(':boutique', $csvfilelines[5], \PDO::PARAM_STR);
            $stmt1->bindValue(':dr', $csvfilelines[6], \PDO::PARAM_STR);
            $stmt1->bindValue(':brand', $csvfilelines[7], \PDO::PARAM_STR);
            $stmt1->bindValue(':enabled', 1, \PDO::PARAM_INT);
            $stmt1->bindValue(':created_at', $date, \PDO::PARAM_STR);
            $stmt1->bindValue(':updated_at', $date, \PDO::PARAM_STR);
            $stmt1->bindValue(':salt', $salt, \PDO::PARAM_STR);
            $stmt1->bindValue(':password', "to_change", \PDO::PARAM_STR);
            $stmt1->bindValue(':roles', "a:0:{}", \PDO::PARAM_STR);
            

            $stmt2->bindValue(':username', $csvfilelines[0], \PDO::PARAM_STR);
            $stmt2->bindValue(':date', $csvfilelines[8], \PDO::PARAM_STR);
            $stmt2->bindValue(':nb_transac_S0', $csvfilelines[9], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_linked_S0', $csvfilelines[10], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npe_S0', $csvfilelines[11], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nve_S0', $csvfilelines[12], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npes_S0', $csvfilelines[13], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nves_S0', $csvfilelines[14], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_npesa_S0', $csvfilelines[15], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_transac_nvesa_S0', $csvfilelines[16], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npe_S0', $csvfilelines[17], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npes_S0', $csvfilelines[18], \PDO::PARAM_STR);
            $stmt2->bindValue(':rank_npesa_S0', $csvfilelines[19], \PDO::PARAM_STR);
            
            try
            {
                $stmt1->execute();
            }
            catch(Exception $e)
            {       
                $output->writeln($e->getMessage());
                die('Erreur 1 : '.$e->getMessage());
            }

            try
            {
                $stmt2->execute();
            }
            catch(Exception $e)
            {       
                $output->writeln($e->getMessage());
                die('Erreur 2 : '.$e->getMessage());
            }

            if($i % 20 == 0){
                $output->writeln($i." lignes importees");
                gc_collect_cycles();
            }
            $i++;
        }
        $output->writeln($i." lignes importees");
        
    }

}