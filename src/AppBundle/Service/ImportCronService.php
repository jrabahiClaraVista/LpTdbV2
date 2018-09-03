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

    public function updateUserTransac( InputInterface $input, OutputInterface $output){

        gc_enable();
        $batchSize = 50;
        $i = 0;

        $date = new \DateTime();
        $date = $date->modify('-1 month');
        $year1 = $date->format("Y");
        $year2 = $year1 + 1;



        $sql1 = " SELECT  a.id, username,email, max(b.nb_transac_ytd) as nb_transac_ytd, max(c.nb_transac_S0) as nb_transac2_ytd
                FROM fos_user_user a
                LEFT JOIN app_kpi_month as b on a.id = b.user_id
                LEFT JOIN app_kpi_week as c on a.id = c.user_id
                WHERE (b.date >= '".$year1."-01-01' and b.date < '".$year2."-01-01') or (c.date >= '".$year1."-01-01' and c.date < '".$year2."-01-01' and b.id is null)
                GROUP BY `id`, `username`, `email`
        ";

        $sql2 = "UPDATE fos_user_user SET nb_transac_ytd = :nb_transac_ytd
                WHERE id = :id ";

        $stmt = $this->pdo->prepare($sql1);
        $stmt2 = $this->pdo->prepare($sql2);

        $i = 0;

        try
        {
            $stmt->execute();
        }
        catch(Exception $e)
        {
            $output->writeln($e->getMessage());
            die('Erreur 1 : '.$e->getMessage());
        }

        while( $result = $stmt->fetch(\PDO::FETCH_ASSOC) )
        {
            $stmt2->bindValue(':id', $result["id"], \PDO::PARAM_INT);

            if($max_transac = $result["nb_transac_ytd"] >= $max_transac = $result["nb_transac2_ytd"]){
              $max_transac = $result["nb_transac_ytd"];
            }
            elseif($max_transac = $result["nb_transac_ytd"] < $max_transac = $result["nb_transac2_ytd"]){
              $max_transac = $result["nb_transac2_ytd"];
            }
            else{
              $max_transac = 0;
            }

            $stmt2->bindValue(':nb_transac_ytd', $max_transac, \PDO::PARAM_INT);

            try
            {
                $stmt2->execute();
            }
            catch(Exception $e)
            {
                $output->writeln($e->getMessage());
                die('Erreur 2 : '.$e->getMessage());
            }

            if (($i % $batchSize) === 0) {
                gc_collect_cycles();
                $output->writeln($i." user mis a jour");
            }
            $i++;
        }

        gc_collect_cycles();
        $output->writeln($i." user mis a jour");

    }

    public function scanDir(){

        if($this->ip == "127.0.0.1")
        {
            $this->filesList = scandir("D:\wamp64\www\LpTdbV3\web\imports");
        }
        else{
            $this->filesList = scandir("/data/ftp/imports");
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
            rename ("/data/ftp/imports/TABLEAU_DE_BORD_lp_".$name."_rq.csv" , "/data/ftp/imports/archives/TABLEAU_DE_BORD_lp_".$name."_rq_".$date.".csv" );
        else
            rename ("/data/ftp/imports/TABLEAU_DE_BORD_lp_rq.csv" , "/data/ftp/imports/archives/TABLEAU_DE_BORD_lp_rq_".$date.".csv" );
    }

    public function renameLastImportWeek($name = null)
    {
        $date = new \DateTime();
        $dateWeek = new \DateTime();
        $date = $date->format("Ym");
        $dateWeek = $dateWeek->format("YmdW");

        if($name != null)
            rename ("/data/ftp/imports/TABLEAU_DE_BORD_hebdo_lp_".$name."_rq.csv" , "/data/ftp/imports/archives/TABLEAU_DE_BORD_hebdo_lp_".$name."_rq_".$dateWeek.".csv" );
        else
            rename ("/data/ftp/imports/TABLEAU_DE_BORD_hebdo_lp_rq.csv" , "/data/ftp/imports/archives/TABLEAU_DE_BORD_hebdo_lp_rq_".$dateWeek.".csv" );
    }

    public function renameLastImportVerbatim()
    {
        $date = new \DateTime();
        $dateWeek = new \DateTime();
        $date = $date->format("Ym");
        $dateWeek = $dateWeek->format("YmdW");

        rename ("/data/ftp/imports/Verbatim_Mois.csv" , "/data/ftp/imports/archives/Verbatim_Mois_".$date.".csv" );
    }


    public function importKpiCaptureCSVFile( InputInterface $input, OutputInterface $output, $csv = null)
    {
        $date = new \DateTime();
        $date = $date->format("Ymd");


        $file = fopen($csv, "r");

        $header1 = "username_canonical,username,prenom_vendeur,nom_vendeur,email,email_canonical,role,boutique,dr,brand,enabled,updated_at";
        $header2 = "date,nb_transac_m0,nb_transac_ytd,tx_transac_linked_m0,tx_transac_linked_ytd,tx_transac_npe_m0,tx_transac_nve_m0,tx_transac_npe_ytd,tx_transac_nve_ytd,tx_transac_npes_m0,tx_transac_nves_m0,tx_transac_npes_ytd,tx_transac_nves_ytd,tx_transac_npesa_m0,tx_transac_nvesa_m0,tx_transac_npesa_ytd,tx_transac_nvesa_ytd,rank_npe_m0,rank_npes_m0,rank_npesa_m0,rank_npe_ytd,rank_npes_ytd,rank_npesa_ytd,nbre_clients_contactables_email_h,nbre_clients_inactifs_email_h,nbre_clients_animes_m0,nbre_clients_transformes_m0,CA_clients_transformes_m0,ca_crm_ytd,nbre_questsatisf_m0,nbre_questsatisf_ytd,nbre_questsatisf_montred_m0,nbre_questsatisf_montre_ytd,nbre_questsatisf_piled_m0,nbre_questsatisf_pile_ytd,tx_quest_satisf_promoteur_m0,tx_quest_satisf_promoteur_ytd,tx_quest_satisf_passif_m0,tx_quest_satisf_passif_ytd,tx_quest_satisf_detracteur_m0,tx_quest_satisf_detracteur_ytd,quest_satisf_nps_m0,quest_satisf_nps_ytd,quest_satisf_rank_nps_m0,quest_satisf_rank_nps_ytd,moy_quest_satisf_montre_q2_m0,moy_quest_satisf_montre_q3_m0,moy_quest_satisf_montre_q4_m0,moy_quest_satisf_montre_q5_m0,moy_quest_satisf_montre_q6_m0,moy_quest_satisf_montre_q2_ytd,moy_quest_satisf_montre_q3_ytd,moy_quest_satisf_montre_q4_ytd,moy_quest_satisf_montre_q5_ytd,moy_quest_satisf_montre_q6_ytd,moy_quest_satisf_pile_q2_m0,moy_quest_satisf_pile_q3_m0,moy_quest_satisf_pile_q4_m0,moy_quest_satisf_pile_q2_ytd,moy_quest_satisf_pile_q3_ytd,moy_quest_satisf_pile_q4_ytd";

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

        // Pour update des données sans écrasés les anciennes
        $header3 = "nbre_questsatisf_m0,nbre_questsatisf_ytd,nbre_questsatisf_montred_m0,nbre_questsatisf_montre_ytd,nbre_questsatisf_piled_m0,nbre_questsatisf_pile_ytd,tx_quest_satisf_promoteur_m0,tx_quest_satisf_promoteur_ytd,tx_quest_satisf_passif_m0,tx_quest_satisf_passif_ytd,tx_quest_satisf_detracteur_m0,tx_quest_satisf_detracteur_ytd,quest_satisf_nps_m0,quest_satisf_nps_ytd,quest_satisf_rank_nps_m0,quest_satisf_rank_nps_ytd,moy_quest_satisf_montre_q2_m0,moy_quest_satisf_montre_q3_m0,moy_quest_satisf_montre_q4_m0,moy_quest_satisf_montre_q5_m0,moy_quest_satisf_montre_q6_m0,moy_quest_satisf_montre_q2_ytd,moy_quest_satisf_montre_q3_ytd,moy_quest_satisf_montre_q4_ytd,moy_quest_satisf_montre_q5_ytd,moy_quest_satisf_montre_q6_ytd,moy_quest_satisf_pile_q2_m0,moy_quest_satisf_pile_q3_m0,moy_quest_satisf_pile_q4_m0,moy_quest_satisf_pile_q2_ytd,moy_quest_satisf_pile_q3_ytd,moy_quest_satisf_pile_q4_ytd";

        //valeurs de la requête (correspond au header du fichier)
        $values2 = ":".str_replace(",", ",:", $header2);
        $values2 = str_replace(":user_id,", "", $values2);
        $values3 = ":".str_replace(",", ",:", $header3);
        $values3 = str_replace(":user_id,", "", $values3);
        //tableau des headers à mettre à jours pour la boucle
        $headers = explode(",", str_replace("user_id,", "", $header2));
        $headers3 = explode(",", str_replace("user_id,", "", $header3));
        $update2 = "";
        $update3 = "";
        $i = 0;
        $len = count($headers);

        foreach ($headers as $key => $value) {
            if ($i == $len - 1) $update2 .= $value." = :".$value;
            else $update2 .= $value." = :".$value.",";
            $i++;
        }

        $i = 0;
        $len = count($headers3);

        foreach ($headers3 as $key => $value) {
            if ($i == $len - 1) $update3 .= $value." = :".$value;
            else $update3 .= $value." = :".$value.",";
            $i++;
        }


        $sql1 = "INSERT INTO fos_user_user ( ".$header1.", created_at, salt, password, roles,locked,expired,credentials_expired,ispremium ) VALUES ( ".$values1.", :created_at, :salt, :password, :roles,0,0,0,0 )
                ON DUPLICATE KEY UPDATE ".$update1."
        ";
        $sql2 = "INSERT INTO app_kpi_month ( user_id, ".$header2." ) VALUES (  (SELECT id from fos_user_user u WHERE u.username = :username) , ".$values2.")
                ON DUPLICATE KEY UPDATE ".$update2."
        "; // Mettre $update3 pour updater uniquement des données sur le nps et le verbateam

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

            $stmt2->bindValue(':nbre_questsatisf_m0',$csvfilelines[37], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_questsatisf_ytd',$csvfilelines[38], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_questsatisf_montred_m0',$csvfilelines[39], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_questsatisf_montre_ytd',$csvfilelines[40], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_questsatisf_piled_m0',$csvfilelines[41], \PDO::PARAM_STR);
            $stmt2->bindValue(':nbre_questsatisf_pile_ytd',$csvfilelines[42], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_quest_satisf_promoteur_m0',$csvfilelines[43], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_quest_satisf_promoteur_ytd',$csvfilelines[44], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_quest_satisf_passif_m0',$csvfilelines[45], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_quest_satisf_passif_ytd',$csvfilelines[46], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_quest_satisf_detracteur_m0',$csvfilelines[47], \PDO::PARAM_STR);
            $stmt2->bindValue(':tx_quest_satisf_detracteur_ytd',$csvfilelines[48], \PDO::PARAM_STR);
            $stmt2->bindValue(':quest_satisf_nps_m0',$csvfilelines[49], \PDO::PARAM_STR);
            $stmt2->bindValue(':quest_satisf_nps_ytd',$csvfilelines[50], \PDO::PARAM_STR);
            $stmt2->bindValue(':quest_satisf_rank_nps_m0',$csvfilelines[51], \PDO::PARAM_STR);
            $stmt2->bindValue(':quest_satisf_rank_nps_ytd',$csvfilelines[52], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q2_m0',$csvfilelines[53], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q3_m0',$csvfilelines[54], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q4_m0',$csvfilelines[55], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q5_m0',$csvfilelines[56], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q6_m0',$csvfilelines[57], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q2_ytd',$csvfilelines[58], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q3_ytd',$csvfilelines[59], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q4_ytd',$csvfilelines[60], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q5_ytd',$csvfilelines[61], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_montre_q6_ytd',$csvfilelines[62], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_pile_q2_m0',$csvfilelines[63], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_pile_q3_m0',$csvfilelines[64], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_pile_q4_m0',$csvfilelines[65], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_pile_q2_ytd',$csvfilelines[66], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_pile_q3_ytd',$csvfilelines[67], \PDO::PARAM_STR);
            $stmt2->bindValue(':moy_quest_satisf_pile_q4_ytd',$csvfilelines[68], \PDO::PARAM_STR);

            //$output->writeln($sql2);die();

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

            if($i % 100 == 0){
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

    public function importVerbatim(InputInterface $input, OutputInterface $output, $csv = null){

        $date = new \DateTime();
        $date = $date->format("Ymd");


        $file = fopen($csv, "r");

        $header1 = "type,marque,dr,boutique,question,note,verbatim,date";

        //tableau des headers à mettre à jours pour la boucle
        $headers = explode(",", str_replace("user_id,", "", $header1));
        $values1 = ":".str_replace(",", ",:", $header1);
        $update1 = "";
        $i = 0;
        $len = count($headers);

        foreach ($headers as $key => $value) {
            if ($i == $len - 1) $update1 .= $value." = :".$value;
            else $update1 .= $value." = :".$value.",";
            $i++;
        }



        $sql1 = "INSERT INTO app_verbatim ( ".$header1." ) VALUES ( ".$values1." )
                ON DUPLICATE KEY UPDATE ".$update1."
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

            $stmt1->bindValue(':type', $csvfilelines[0], \PDO::PARAM_STR);
            $stmt1->bindValue(':marque', $csvfilelines[1], \PDO::PARAM_STR);
            $stmt1->bindValue(':dr', $csvfilelines[2], \PDO::PARAM_STR);
            $stmt1->bindValue(':boutique', $csvfilelines[3], \PDO::PARAM_STR);
            $stmt1->bindValue(':question', $csvfilelines[4], \PDO::PARAM_STR);
            $stmt1->bindValue(':note', $csvfilelines[5], \PDO::PARAM_STR);
            $stmt1->bindValue(':verbatim', $csvfilelines[6], \PDO::PARAM_STR);
            $stmt1->bindValue(':date', $csvfilelines[7], \PDO::PARAM_STR);

            //$output->writeln($sql1);

            try
            {
                $stmt1->execute();
            }
            catch(Exception $e)
            {
                $output->writeln($e->getMessage());
                die('Erreur 1 : '.$e->getMessage());
            }

            if($i % 300 == 0){
                $output->writeln($i." lignes importees");
                gc_collect_cycles();
            }
            $i++;
        }
        $output->writeln($i." lignes importees");
    }

    public function deleteHistoDays(InputInterface $input, OutputInterface $output, $days){

        $date = new \DateTime();
        $date = $date->format("Ymd");

        $sql1 = "DELETE from app_client 
                WHERE modified_at <= DATE_SUB( now(), INTERVAL $days DAY ) 
        ";

        try
        {
            $stmt1->execute();
        }
        catch(Exception $e)
        {
            $output->writeln($e->getMessage());
            die('Erreur 1 : '.$e->getMessage());
        }

        $output->writeln("Les anciennes lignes ont été supprimees");
    }

}
