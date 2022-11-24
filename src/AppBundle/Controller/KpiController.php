<?php

namespace AppBundle\Controller;


// src/OC/PlatformBundle/Controller/AdvertController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\Sonata\UserBundle\Entity\User as User;
use AppBundle\Entity\KpiWeek;
use AppBundle\Entity\KpiMonth;
use AppBundle\Entity\KpiTrim;

use AppBundle\Form\CampaignKpiType;
use AppBundle\Form\KpiFilterType;
use AppBundle\Form\ExportDataType;
use AppBundle\Form\ExportVerbatimType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

// Annotaitonss :
// Pour gérer les autorisations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
// Pour gérer le ParamConverter et utiliser un entité en parametre à la place d'une simple variable
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class KpiController extends Controller
{
	/**
	 * @ParamConverter("user_actuel", options={"mapping": {"user_actuel": "id"}})
     */
	public function kpiAction(User $user_actuel, $user_id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();
		$routeName = $request->get('_route');

		if($user_id == 0) {$user = $user_actuel;}
		else {$user = $em->getRepository('ApplicationSonataUserBundle:User')->findOneById($user_id);}

		$session->remove('filtre_reseau');
		$session->remove('filtre_dr');
		$session->remove('filtre_boutique');
		$session->remove('filtre_vendeur');

		if($routeName == "app_kpi_fid"){
			if( $user->getRole() == 'ROLE_VENDEUR' ) {
				$boutique = $em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(array("username" => $user->getBoutique()));
				return $this->redirectToRoute('app_kpi_fid', array('user_actuel' =>  $user_actuel->getId(),'user_id' => $boutique->getId()));
			}
		}

		$lastKpi = $em->getRepository('AppBundle:KpiMonth')->findOneBy(array('user' => $user), array('date' => "DESC"));

		if($lastKpi == null){
			$session->remove('kpi_month_filtre');
			$session->remove('kpi_year_filtre');

			return $this->render('AppBundle:Kpi:no_data.html.twig', array(
	        	'user'				=> $user,
	        	)
	        );
		}

		$date = $lastKpi->getDate();

		//initialisation des variable de session
		$kpiFilterService = $this->container->get('app.kpi_filter_session');
		$vars = $kpiFilterService->initVars($user, $request);

        $reseau      = $vars[0];
        $dr 	     = $vars[1];
        $boutique    = $vars[2];
        $vendeur     = $vars[3];

        //simplification du code par utilisation d'un service pour initialiser les dates utiliser pour filtrer des données
		$kpiDates = $this->get('app.init_Kpi_dates');
		$dates = $kpiDates->getDates($date, $session, 0);

		$month = $dates['month'];
		$year  = $dates['year'];
		$date1 = $dates['date1'];//Premier jour du mois à J - 1 an
		$date2 = $dates['date2'];//Dernier jour du mois
		$date3 = $dates['date3'];//Premier jour du mois

		if($session->get('kpi_year_filtre') != null)
			$form = $this->createForm(new KpiFilterType($em, $user, $user, null, $month, null, $session->get('kpi_year_filtre') , 'mensuel'));
		else
			$form = $this->createForm(new KpiFilterType($em, $user, $user, null, $month, null, $year, 'mensuel'));

		$form->handleRequest($request);
		//Recuperation des données de la requete
        $data = $form->getData();

        $currentMonth = $lastKpi->getDate()->format("m");

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';


		$vendeurBoutique = $user->getBoutique();
		if ($vendeurBoutique == null) $vendeurBoutique = '';

		$getBoutiquesDr = null;
		$getDrsMarque = null;

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$getDrsMarque = $em->getRepository('AppBundle:KpiMonth')->getKpiDrMarque($date3, $date2, $brand);
			$getBoutiquesDr = array();
			$getVendeursBoutique = array();

			$kpisCSV = $em->getRepository('AppBundle:KpiMonth')->getKpisMarque($date3, $date2, $brand);

			foreach ($getDrsMarque as $key => $dr) {
				$getBoutiques = $em->getRepository('AppBundle:KpiMonth')->getKpiBoutiqueDr($dr->getUser()->getUsername(), $date3, $date2, $brand);
				$getBoutiquesDr[$key] = $getBoutiques;


				/*foreach ($getBoutiques as $key2 => $boutique) {
					$getVendeurs =  $em->getRepository('AppBundle:KpiMonth')->getKpiVendeurBoutique($boutique->getUser()->getUsername(), $date3, $date2, $brand);
					$getVendeursBoutique[$key2] = $getVendeurs;
				}*/
			}
		}
		if( $user->getRole() == 'ROLE_DR' ) {
			$getBoutiquesDr = $em->getRepository('AppBundle:KpiMonth')->getKpiBoutiqueDr($user->getUsername(), $date3, $date2, $brand);
			$getVendeursBoutique = array();
			$getDrsMarque = null;

			foreach ($getBoutiquesDr as $key2 => $boutique) {
				$getVendeurs =  $em->getRepository('AppBundle:KpiMonth')->getKpiVendeurBoutique($boutique->getUser()->getUsername(), $date3, $date2, $brand);
				$getVendeursBoutique[$key2] = $getVendeurs;
			}

			$kpisCSV = $em->getRepository('AppBundle:KpiMonth')->getKpisDr($date3, $date2, $user->getUsername(), $brand);

		}
		if( $user->getRole() == 'ROLE_BOUTIQUE' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiMonth')->getKpiVendeurBoutique($user->getUsername(), $date3, $date2, $brand);
			$getBoutiquesDr = null;
			$getDrsMarque = null;

			$kpisCSV = $em->getRepository('AppBundle:KpiMonth')->getKpisBoutique($date3, $date2, $user->getUsername(), $brand);
		}
		if( $user->getRole() == 'ROLE_VENDEUR' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiMonth')->getKpiVendeurBoutique($user->getBoutique(), $date3, $date2, $brand);
			$getBoutiquesDr = null;
			$getDrsMarque = null;

			$kpisCSV = $em->getRepository('AppBundle:KpiMonth')->getKpisBoutique($date3, $date2, $user->getBoutique(), $brand);
		}

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$marque = $em->getRepository('AppBundle:KpiMonth')->getKpiMarque($date3, $date2, $user->getUsername());
		}
		else{
			$marque = $em->getRepository('AppBundle:KpiMonth')->getKpiMarque($date3, $date2, $user->getBrand(), $brand);
		}

        if ( $request->getMethod() == 'POST' && $form->isSubmitted() ) {
            //Mise à jour des variable de session
            $kpiFilterService->updateSessionVars($data);
            $reseau    = $session->get('filtre_reseau');
	        $dr 	   = $session->get('filtre_dr');
	        $boutique  = $session->get('filtre_boutique');
	        $vendeur   = $session->get('filtre_vendeur');

        	$dates = $kpiDates->getDatesPost($data, $session, 0);
        	$month = $dates['month'];
			$year  = $dates['year'];
			$date1 = $dates['date1'];
			$date2 = $dates['date2'];
			$date3 = $dates['date3'];

			if($session->get('filtre_vendeur') != null){
				$id = $vendeur->getId();
	        }
	        elseif($session->get('filtre_boutique') != null){
				$id = $boutique->getId();
	        }
	        elseif($session->get('filtre_dr') != null){
	        	$id = $dr->getId();
	        }
	        elseif($session->get('filtre_reseau') != null){
	        	$id = $reseau->getId();
	        }
	        else{
	        	$id = $user->getId();
	        }

	        /* ICI MISE A JOUR DU RECHARGEMENT DE PAGE POUR LE FILTRE */

			if($routeName == "app_kpi_month"){
				return $this->redirectToRoute('app_kpi_month', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$id));
			}
			if($routeName == "app_kpi_ytd"){
				return $this->redirectToRoute('app_kpi_ytd', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$id));
			}
			if($routeName == "app_kpi_fid"){
				return $this->redirectToRoute('app_kpi_fid', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$id));
			}
			if($routeName == "app_kpi_planning"){
				return $this->redirectToRoute('app_kpi_planning', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$id));
			}
			if($routeName == "app_kpi_satisfaction"){
				return $this->redirectToRoute('app_kpi_satisfaction', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$id));
			}
        }

        //Gestion des requêtes selon la page appelée

        if($session->get('filtre_vendeur') != null){
        	$kpis = $em->getRepository('AppBundle:KpiMonth')->getUserKpisBetweenDates($session->get('filtre_vendeur'), $date1, $date2, $brand);
        }
        elseif($session->get('filtre_boutique') != null){
        	$kpis = $em->getRepository('AppBundle:KpiMonth')->getUserKpisBetweenDates($session->get('filtre_boutique'), $date1, $date2, $brand);
        }
        elseif($session->get('filtre_dr') != null){
        	$kpis = $em->getRepository('AppBundle:KpiMonth')->getUserKpisBetweenDates($session->get('filtre_dr'), $date1, $date2, $brand);
        }
        elseif($session->get('filtre_reseau') != null){
        	$kpis = $em->getRepository('AppBundle:KpiMonth')->getUserKpisBetweenDates($session->get('filtre_reseau'), $date1, $date2, $brand);
        }
        else{
        	$kpis = $em->getRepository('AppBundle:KpiMonth')->getUserKpisBetweenDates($user, $date1, $date2, $brand);
        }


		//get current month depending on url parameter
		foreach ($kpis as $key => $kpi) {

			if ( $month == null ) {
				if ( $key == 0 )
					$kpiCurrentMonth = $kpi;
				$month = $kpiCurrentMonth->getDate()->format("m");
			}
			else {
				if ( $kpi->getDate()->format("m") == $month && $kpi->getDate()->format("Y") == $year ) {
					$kpiCurrentMonth = $kpi;
				}
			}
		}

		if ($kpis == null or $kpiCurrentMonth == null){
			//throw new NotFoundHttpException("No data Available");
			//$kpiCurrentMonth = null;
			$session->remove('kpi_month_filtre');
            $session->remove('kpi_year_filtre');

            if($routeName == "app_kpi_month"){
				return $this->redirectToRoute('app_kpi_month', array('user_actuel' => $user_actuel->getId()));
			}
			if($routeName == "app_kpi_ytd"){
				return $this->redirectToRoute('app_kpi_ytd', array('user_actuel' => $user_actuel->getId()));
			}
			if($routeName == "app_kpi_fid"){
				return $this->redirectToRoute('app_kpi_fid', array('user_actuel' => $user_actuel->getId()));
			}
			if($routeName == "app_kpi_planning"){
				return $this->redirectToRoute('app_kpi_planning', array('user_actuel' => $user_actuel->getId()));
			}
		}

		//Récupération des top
		if($routeName == "app_kpi_month"){
			$topNpe = $em->getRepository('AppBundle:KpiMonth')->getRank1Npe($date3, $date2, $brand);
			$topNpes = $em->getRepository('AppBundle:KpiMonth')->getRank1Npes($date3, $date2, $brand);
			$topNpesa = $em->getRepository('AppBundle:KpiMonth')->getRank1Npesa($date3, $date2, $brand);

			$topNpeVendeur = $em->getRepository('AppBundle:KpiMonth')->getRank1NpeVendeur($date3, $date2, $brand);
			$topNpesVendeur = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesVendeur($date3, $date2, $brand);
			$topNpesaVendeur = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesaVendeur($date3, $date2, $brand);

			$topNpe2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npe2($date3, $date2, $brand);
			$topNps2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Nps2($date3, $date2, $brand);
			$topNpes2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npes2($date3, $date2, $brand);

			$topNpeVendeur2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npe2Vendeur($date3, $date2, $brand, $vendeurBoutique);
			$topNpsVendeur2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Nps2Vendeur($date3, $date2, $brand, $vendeurBoutique);
			$topNpesVendeur2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npes2Vendeur($date3, $date2, $brand, $vendeurBoutique);
		}
		if($routeName == "app_kpi_ytd"){
			$topNpe = $em->getRepository('AppBundle:KpiMonth')->getRank1NpeYtd($date3, $date2, $brand);
			$topNpes = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesYtd($date3, $date2, $brand);
			$topNpesa = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesaYtd($date3, $date2, $brand);

			$topNpeVendeur = $em->getRepository('AppBundle:KpiMonth')->getRank1NpeYtdVendeur($date3, $date2, $brand);
			$topNpesVendeur = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesYtdVendeur($date3, $date2, $brand);
			$topNpesaVendeur = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesaYtdVendeur($date3, $date2, $brand);

			$topNpe2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npe2Ytd($date3, $date2, $brand);
			$topNps2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Nps2Ytd($date3, $date2, $brand);
			$topNpes2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npes2Ytd($date3, $date2, $brand);

			$topNpeVendeur2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npe2YtdVendeur($date3, $date2, $brand, $vendeurBoutique);
			$topNpsVendeur2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Nps2YtdVendeur($date3, $date2, $brand, $vendeurBoutique);
			$topNpesVendeur2 = $em->getRepository('AppBundle:KpiMonth')->getRank1Npes2YtdVendeur($date3, $date2, $brand, $vendeurBoutique);
		}
		if($routeName == "app_kpi_fid"){
			$kpisTopCa = $em->getRepository('AppBundle:KpiMonth')->getTop3Ca($brand, $date);
		}
		if($routeName == "app_kpi_planning"){
			$campaigns = $em->getRepository('AppBundle:Campaign')->getCampaignsOfMonth($date3, $date2, $user->getBrand());
			$campaignFile = $em->getRepository('AppBundle:CampaignFile')->getCampaignOfMonth($date3, $date2, $user->getBrand());
		}
		if($routeName == "app_kpi_satisfaction"){
			$topNPS = $em->getRepository('AppBundle:KpiMonth')->getRank1_3NPS($date3, $date2, $brand);
		}

		//Mise à jour du filtre
		$form = $kpiFilterService->updateForm($user, $request, $form);

		$form2 = $this->createForm(new ExportDataType());
        $form2->handleRequest($request);

		//Export CSV
		if ($form2->isSubmitted()) {
			
			$ids = "(";

			foreach ($kpisCSV as $key => $id_kpi){
				if($key == 0){
					$ids .= $id_kpi['id'];
				}
				else{
					$ids .= ",".$id_kpi['id'];
				}
			}
			$ids .= ")";
			
			// OLD KPIs export
			if( $kpiCurrentMonth->getDate() < new \Datetime('2019-01-01') || $request->get('old') == t)
			{
				if($routeName == "app_kpi_month"){
					$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_m0,d.tx_transac_linked_m0,d.tx_transac_npe_m0,d.tx_transac_npes_m0,d.tx_transac_npesa_m0
							FROM app_kpi_month d
							LEFT JOIN fos_user_user u on d.user_id = u.id
							WHERE d.id in $ids";
					$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS Mensuel',
		            					'TAUX DE TRANSACTIONS LIÉES Mensuel','CAPTURE EMAIL VALIDE Mensuel','CAPTURE EMAIL + SMS VALIDE Mensuel','CAPTURE EMAIL + SMS + ADRESSE VALIDE Mensuel');
				}
				if($routeName == "app_kpi_ytd"){
					$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_ytd,d.tx_transac_linked_ytd,d.tx_transac_npe_ytd,d.tx_transac_npes_ytd,d.tx_transac_npesa_ytd
							FROM app_kpi_month d
							LEFT JOIN fos_user_user u on d.user_id = u.id
							WHERE d.id in $ids";
					$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS YtD',
		            					'TAUX DE TRANSACTIONS LIÉES YtD','CAPTURE EMAIL VALIDE YtD','CAPTURE EMAIL + SMS VALIDE YtD','CAPTURE EMAIL + SMS + ADRESSE VALIDE YtD');
				}
				if($routeName == "app_kpi_fid"){
					$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nbre_clients_contactables_email_h,d.nbre_clients_animes_m0,
									d.nbre_clients_transformes_m0,d.ca_clients_transformes_m0,d.ca_Crm_ytd
							FROM app_kpi_month d
							LEFT JOIN fos_user_user u on d.user_id = u.id
							WHERE d.id in $ids";
					$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE CLIENTS CONTACTABLES PAR EMAIL',
		            					'CLIENTS CONTACTÉS PAR EMAIL SUR LE MOIS','CLIENTS CONTACTÉS PAR EMAIL AYANT ACHETÉ SUR LE MOIS','CA DES CLIENTS CONTACTÉS PAR EMAIL AYANT ACHETÉ SUR LE MOIS',
		            					'CA GÉNÉRÉ PAR LES CLIENTS CONTACTÉS PAR EMAIL DEPUIS JANVIER');
				}
				

	            //Creation du fichier CSV et du header
	            $handle     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle, $header, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt = $pdo->prepare($sql);
	            $stmt->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	            	if($routeName == "app_kpi_month"){
	                $row["nb_transac_m0"] = str_replace('.',',',$row["nb_transac_m0"]);
	                $row["tx_transac_linked_m0"] = str_replace('.',',',$row["tx_transac_linked_m0"]);
	                $row["tx_transac_npe_m0"] = str_replace('.',',',$row["tx_transac_npe_m0"]);
	                $row["tx_transac_npes_m0"]  = str_replace('.',',',$row["tx_transac_npes_m0"]);
	                $row["tx_transac_npesa_m0"]  = str_replace('.',',',$row["tx_transac_npesa_m0"]);
	            	}
	            	if($routeName == "app_kpi_ytd"){
	                $row["nb_transac_ytd"] = str_replace('.',',',$row["nb_transac_ytd"]);
	                $row["tx_transac_linked_ytd"] = str_replace('.',',',$row["tx_transac_linked_ytd"]);
	                $row["tx_transac_npe_ytd"] = str_replace('.',',',$row["tx_transac_npe_ytd"]);
	                $row["tx_transac_npes_ytd"]  = str_replace('.',',',$row["tx_transac_npes_ytd"]);
	                $row["tx_transac_npesa_ytd"]  = str_replace('.',',',$row["tx_transac_npesa_ytd"]);
	            	}
	                fputcsv($handle, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle);
	            $content = stream_get_contents($handle);
	            fclose($handle);
			}
			// NEW KPIs export
			else{
				if($routeName == "app_kpi_month"){
					$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_m0,d.tx_transac_linked_m0,d.tx_transac_npesi2_m0,d.tx_transac_npei_m0,d.tx_transac_npsi_m0
							FROM app_kpi_month d
							LEFT JOIN fos_user_user u on d.user_id = u.id
							WHERE d.id in $ids";
					$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS Mensuel',
		            					'TAUX DE TRANSACTIONS LIÉES Mensuel','CAPTURE EMAIL ET/OU SMS VALIDE et OPTIN Mensuel','CAPTURE EMAIL VALIDE et OPTIN Mensuel','CAPTURE SMS VALIDE et OPTIN Mensuel');
				}
				if($routeName == "app_kpi_ytd"){
					$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_ytd,d.tx_transac_linked_ytd,d.tx_transac_npesi2_ytd,d.tx_transac_npei_ytd,d.tx_transac_npsi_ytd
							FROM app_kpi_month d
							LEFT JOIN fos_user_user u on d.user_id = u.id
							WHERE d.id in $ids";
					$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS YtD',
		            					'TAUX DE TRANSACTIONS LIÉES YtD','CAPTURE EMAIL ET/OU SMS VALIDE et OPTIN YtD','CAPTURE EMAIL VALIDE et OPTIN YtD','CAPTURE SMS VALIDE et OPTIN YtD');
				}
				if($routeName == "app_kpi_fid"){
					$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nbre_clients_contactables_email_h,d.nbre_clients_animes_m0,
									d.nbre_clients_transformes_m0,d.ca_clients_transformes_m0,d.ca_Crm_ytd
							FROM app_kpi_month d
							LEFT JOIN fos_user_user u on d.user_id = u.id
							WHERE d.id in $ids";
					$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE CLIENTS CONTACTABLES PAR EMAIL',
		            					'CLIENTS CONTACTÉS PAR EMAIL SUR LE MOIS','CLIENTS CONTACTÉS PAR EMAIL AYANT ACHETÉ SUR LE MOIS','CA DES CLIENTS CONTACTÉS PAR EMAIL AYANT ACHETÉ SUR LE MOIS',
		            					'CA GÉNÉRÉ PAR LES CLIENTS CONTACTÉS PAR EMAIL DEPUIS JANVIER');
				}
				

	            //Creation du fichier CSV et du header
	            $handle     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle, $header, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt = $pdo->prepare($sql);
	            $stmt->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	            	if($routeName == "app_kpi_month"){
	                $row["nb_transac_m0"] = str_replace('.',',',$row["nb_transac_m0"]);
	                $row["tx_transac_linked_m0"] = str_replace('.',',',$row["tx_transac_linked_m0"]);
	                $row["tx_transac_npesi2_m0"] = str_replace('.',',',$row["tx_transac_npesi2_m0"]);
	                $row["tx_transac_npei_m0"]  = str_replace('.',',',$row["tx_transac_npei_m0"]);
	                $row["tx_transac_npsi_m0"]  = str_replace('.',',',$row["tx_transac_npsi_m0"]);
	            	}
	            	if($routeName == "app_kpi_ytd"){
	                $row["nb_transac_ytd"] = str_replace('.',',',$row["nb_transac_ytd"]);
	                $row["tx_transac_linked_ytd"] = str_replace('.',',',$row["tx_transac_linked_ytd"]);
	                $row["tx_transac_npesi2_ytd"] = str_replace('.',',',$row["tx_transac_npesi2_ytd"]);
	                $row["tx_transac_npei_ytd"]  = str_replace('.',',',$row["tx_transac_npei_ytd"]);
	                $row["tx_transac_npsi_ytd"]  = str_replace('.',',',$row["tx_transac_npsi_ytd"]);
	            	}
	                fputcsv($handle, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle);
	            $content = stream_get_contents($handle);
	            fclose($handle);
			}

            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            //Reponse : Téléchargement du fichier
            return new Response($content, 200, array(
                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="export_donnees_'.$date_csv.'.csv"'
            ));

        }

        if($routeName == "app_kpi_satisfaction"){
	        //Mise à jour du filtre
			//$kpiFilterService->updateFormVerbatime($user, $request);

			$formMontre = $this->createForm(new ExportVerbatimType(1));
	        $formMontre->handleRequest($request);

			$formMontreAll = $this->createForm(new ExportVerbatimType(2));
	        $formMontreAll->handleRequest($request);

			$formPile = $this->createForm(new ExportVerbatimType(3));
	        $formPile->handleRequest($request);

			$formPileAll = $this->createForm(new ExportVerbatimType(4));
	        $formPileAll->handleRequest($request);

			$formRank = $this->createForm(new ExportVerbatimType(5));
	        $formRank->handleRequest($request);

			//Export Verbatim CSV

			if ($formMontre->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
			}

			if ($formMontreAll->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Montre'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Montre'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Montre'";
				}
			}

			if ($formPile->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
			}

			if ($formPileAll->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Pile/Bracelet'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Pile/Bracelet'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Pile/Bracelet'";
				}
			}


			$header2     = array('Reseau','DR','Boutique','Type','Question','Note','Verbatim','Date de validation du questionnaire');

			if($formMontre->isSubmitted() OR $formMontreAll->isSubmitted() OR $formPile->isSubmitted() OR $formPileAll->isSubmitted()){
	            //Creation du fichier CSV et du header2
	            $handle2     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle2, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle2, $header2, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt2 = $pdo->prepare($sql2);

	            $stmt2->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt2->fetch(\PDO::FETCH_ASSOC)) {
	                $row["note"] = str_replace('.',',',$row["note"]);

	                fputcsv($handle2, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle2);
	            $content = stream_get_contents($handle2);
	            fclose($handle2);
	        }

            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            if ($formPileAll->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_pile_bracelet_histo_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formMontreAll->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_montre_histo_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formPile->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_pile_bracelet_'.str_replace(' ','_',$username).'_mois_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formMontre->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_montre_mois_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }

			if ($formRank->isSubmitted()) {

				$username = addslashes($user->getUsername());
				$reseau = $user->getBrand();


				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_m0, k.quest_satisf_nps_m0, k.nbre_questsatisf_m0, k.quest_satisf_rank_nps_ytd, k.quest_satisf_nps_ytd, k.nbre_questsatisf_ytd
						FROM app_kpi_month k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$username' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_m0, k.quest_satisf_nps_m0, k.nbre_questsatisf_m0, k.quest_satisf_rank_nps_ytd, k.quest_satisf_nps_ytd, k.nbre_questsatisf_ytd
						FROM app_kpi_month k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$reseau' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_m0, k.quest_satisf_nps_m0, k.nbre_questsatisf_m0, k.quest_satisf_rank_nps_ytd, k.quest_satisf_nps_ytd, k.nbre_questsatisf_ytd
						FROM app_kpi_month k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$reseau' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}

				$header3     = array('DR','Boutique','Mois','Classement du mois','Note NPS du mois','Nombre de répondants sur le mois','Classement YTD','Note NPS YTD','Nombre de répondants sur l\'année');

		            //Creation du fichier CSV et du header2
		            $handle3     = fopen('php://memory', 'r+');

		            //Creation de l'entête du fichier pour être lisible dans Exel
		            fputs($handle3, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		            fputcsv($handle3, $header3, ';');

		            //Initialisation de la connection à la BDD
		            $pdo = $this->container->get('app.pdo_connect');
		            $pdo = $pdo->initPdoClienteling();

		            //Préparation et execution de la requête
		            $stmt3 = $pdo->prepare($sql3);
		            $stmt3->execute();

		            //Remplissage du fichier csv.
		            while ($row = $stmt3->fetch(\PDO::FETCH_ASSOC)) {
		            	$date_format = $row["date"];
		            	$date_format = new \Datetime($date_format);
		            	$date_format = $date_format->format('m-Y');
                		$row["date"] = $date_format;
                		$row["quest_satisf_nps_m0"] = round($row["quest_satisf_nps_m0"]);
                		$row["quest_satisf_nps_ytd"] = round($row["quest_satisf_nps_ytd"]);
		                fputcsv($handle3, $row, ';');
		            }

		            //Fermeture du fichier
		            rewind($handle3);
		            $content = stream_get_contents($handle3);
		            fclose($handle3);

	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_classement_'.$date_format.'.csv"'
	            ));

			}
		}

		if($kpiCurrentMonth->getDate() < new \Datetime('2019-01-01') || $request->get('old') == 't')
		{
			$path_month = 'AppBundle:Kpi:month.html.twig';
			$path_ytd = 'AppBundle:Kpi:ytd.html.twig';
		}
		else{
			$path_month = 'AppBundle:Kpi:month_2019.html.twig';
			$path_ytd = 'AppBundle:Kpi:ytd_2019.html.twig';
		}
		//Retourne la bonne page
		if($routeName == "app_kpi_month"){
	        return $this->render($path_month, array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentMonth,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'topNpeVendeur'		=> $topNpeVendeur,
	        	'topNpesVendeur'	=> $topNpesVendeur,
	        	'topNpesaVendeur'	=> $topNpesaVendeur,
	        	'topNpe2'			=> $topNpe2,
	        	'topNps2'			=> $topNps2,
	        	'topNpes2'			=> $topNpes2,
	        	'topNpeVendeur2'	=> $topNpeVendeur2,
	        	'topNpsVendeur2'	=> $topNpsVendeur2,
	        	'topNpesVendeur2'	=> $topNpesVendeur2,
	        	'currentMonth'		=> $currentMonth,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'scope'				=> 'mensuel',
	        	'user_actuel'		=> $user_actuel,
	        	'user_role'			=> $user->getRole()
	        	)
	        );
		}
		if($routeName == "app_kpi_ytd"){
	        return $this->render($path_ytd, array(
	        	'currentKpi'		=> $kpiCurrentMonth,
	        	'year'				=> $year,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'topNpeVendeur'		=> $topNpeVendeur,
	        	'topNpesVendeur'	=> $topNpesVendeur,
	        	'topNpesaVendeur'	=> $topNpesaVendeur,
	        	'topNpe2'			=> $topNpe2,
	        	'topNps2'			=> $topNps2,
	        	'topNpes2'			=> $topNpes2,
	        	'topNpeVendeur2'	=> $topNpeVendeur2,
	        	'topNpsVendeur2'	=> $topNpsVendeur2,
	        	'topNpesVendeur2'	=> $topNpesVendeur2,
	        	'user'				=> $user,
	        	'month'				=> $month,
	        	'marque'			=> $marque,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'form'          	=> $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'scope'				=> 'mensuel',
	        	'user_actuel'		=> $user_actuel,
	        	'user_role'			=> $user->getRole()
	        	)
	        );
	    }
	    if($routeName == "app_kpi_satisfaction"){
	        return $this->render('AppBundle:Kpi:satisfaction.html.twig', array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentMonth,
	        	'currentMonth'		=> $currentMonth,
	        	'topNPS'			=> $topNPS,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'formMontre'       	=> $formMontre->createView(),
	        	'formPile'        	=> $formPile->createView(),
	        	'formMontreAll'    	=> $formMontreAll->createView(),
	        	'formPileAll'       => $formPileAll->createView(),
	        	'formRank'      	=> $formRank->createView(),
	        	'scope'				=> 'mensuel',
	        	'user_bis'			=> $user_id,
	        	'user_actuel'		=> $user_actuel,
	        	'user_role'			=> $user->getRole()
	        	)
	        );
		}
		if($routeName == "app_kpi_fid"){
	        return $this->render('AppBundle:Kpi:fid.html.twig', array(
	        	//'kpis' 		=> $kpis,
	        	//'kpisYtd'		=> $kpisYtd,
		        'currentKpi'	=> $kpiCurrentMonth,
	        	'kpisTopCa'		=> $kpisTopCa,
	        	//'topTauxCumul'	=> $tauxCumul,
	        	'user'			=> $user,
	        	'month'			=> $month,
	        	'form'          => $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'marque'		=> $marque,
		        'getBoutiquesDr'	=> $getBoutiquesDr,
		        'getDrsMarque'		=> $getDrsMarque,
		        'getVendeursBoutique' => $getVendeursBoutique,
		        'scope'				=> 'mensuel',
		        'user_actuel'		=> $user_actuel,
		        'user_role'			=> $user->getRole()
	        	)
	        );
		}

		if($routeName == "app_kpi_planning"){
			return $this->render('AppBundle:Kpi:planning.html.twig', array(
		        'currentKpi'		=> $kpiCurrentMonth,
	        	'campaigns' 		=> $campaigns,
	        	'campaignFile' 		=> $campaignFile,
	        	'month'				=> $month,
	        	'currentMonth'		=> $currentMonth,
	        	'form'          	=> $form->createView(),
	        	'user'				=> $user,
	        	'user_actuel'		=> $user_actuel
	        	)
	        );
		}
	}


	/**
	 * @ParamConverter("user_actuel", options={"mapping": {"user_actuel": "id"}})
     */
	public function kpiWeekAction(User $user_actuel, $user_id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();
		$routeName = $request->get('_route');

		//$session->clear();

		if($user_id == 0) {$user = $user_actuel;}
		else {$user = $em->getRepository('ApplicationSonataUserBundle:User')->findOneById($user_id);}

		$session->remove('filtre_reseau');
		$session->remove('filtre_dr');
		$session->remove('filtre_boutique');
		$session->remove('filtre_vendeur');

		$lastKpiWeek = $em->getRepository('AppBundle:KpiWeek')->findOneBy(array('user' => $user), array('date' => "DESC"));

		if($lastKpiWeek == null){
			$session->remove('kpi_week_filtre');
			$session->remove('kpi_year_filtre');

			return $this->render('AppBundle:Kpi:no_data.html.twig', array(
	        	'user'				=> $user,
	        	)
	        );
		}

		// ATTENTION FAIRE UNE PAGE NO DATA POUR DES RESULTATS NULL
		$dateWeek = $lastKpiWeek->getDate();

		//initialisation des variable de session
		$kpiFilterService = $this->container->get('app.kpi_filter_session');
		$vars = $kpiFilterService->initVars($user, $request);

        $reseau      = $vars[0];
        $dr 		 = $vars[1];
        $boutique    = $vars[2];
        $vendeur     = $vars[3];


        //simplification du code par utilisation d'un service pour initialiser les dates utiliser pour filtrer des données
		$kpiDates = $this->get('app.init_Kpi_dates');
		$datesWeek = $kpiDates->getDatesWeek($dateWeek, $session, 0);

		$week 		= $datesWeek['week'];
		$year 		= $datesWeek['year'];
		$dateWeek1 	= $datesWeek['dateWeek1'];//Premier jour du mois à J - X mois
		$dateWeek2 	= $datesWeek['dateWeek2'];//Dernier jour du mois
		$dateWeek3 	= $datesWeek['dateWeek3'];//Premier jour du mois

		if($session->get('kpi_year_filtre') != null){
			$form = $this->createForm(new KpiFilterType($em, $user, $user, $week, null, null, $session->get('kpi_year_filtre') , 'hebdomadaire'));
		}
		else{
			$form = $this->createForm(new KpiFilterType($em, $user, $user, $week, null, null, $year, 'hebdomadaire'));
		}


		$form->handleRequest($request);
		//Recuperation des données de la requete
        $data = $form->getData();

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';


		$vendeurBoutique = $user->getBoutique();
		if ($vendeurBoutique == null) $vendeurBoutique = '';

		$getBoutiquesDr = null;
		$getDrsMarque = null;

		//Requetes Mensuelles / hebdomadaire
		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$getDrsMarque = $em->getRepository('AppBundle:KpiWeek')->getKpiDrMarque($dateWeek3, $dateWeek2, $brand);
			$getBoutiquesDr = array();
			$getVendeursBoutique = null;

			foreach ($getDrsMarque as $key => $dr) {
				$getBoutiques = $em->getRepository('AppBundle:KpiWeek')->getKpiBoutiqueDr($dr->getUser()->getUsername(), $dateWeek3, $dateWeek2, $brand);
				$getBoutiquesDr[$key] = $getBoutiques;
			}

			$kpisCSV = $em->getRepository('AppBundle:KpiWeek')->getKpisMarque($dateWeek1, $dateWeek2, $brand);
		}
		if( $user->getRole() == 'ROLE_DR' ) {
			$getBoutiquesDr = $em->getRepository('AppBundle:KpiWeek')->getKpiBoutiqueDr($user->getUsername(), $dateWeek3, $dateWeek2, $brand);
			$getVendeursBoutique = array();
			$getDrsMarque = null;

			foreach ($getBoutiquesDr as $key2 => $boutique) {
				$getVendeurs = $em->getRepository('AppBundle:KpiWeek')->getKpiVendeurBoutique($boutique->getUser()->getUsername(), $dateWeek3, $dateWeek2, $brand);
				$getVendeursBoutique[$key2] = $getVendeurs;
			}

			$kpisCSV = $em->getRepository('AppBundle:KpiWeek')->getKpisDr($dateWeek1, $dateWeek2, $user->getUsername(), $brand);
		}
		if( $user->getRole() == 'ROLE_BOUTIQUE' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiWeek')->getKpiVendeurBoutique($user->getUsername(), $dateWeek3, $dateWeek2, $brand);
			$getBoutiquesDr = null;
			$getDrsMarque = null;

			$kpisCSV = $em->getRepository('AppBundle:KpiWeek')->getKpisBoutique($dateWeek1, $dateWeek2, $user->getBoutique(), $brand);
		}
		if( $user->getRole() == 'ROLE_VENDEUR' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiWeek')->getKpiVendeurBoutique($user->getBoutique(), $dateWeek3, $dateWeek2, $brand);
			$getBoutiquesDr = null;
			$getDrsMarque = null;

			$kpisCSV = $em->getRepository('AppBundle:KpiWeek')->getKpisBoutique($dateWeek1, $dateWeek2, $user->getBoutique(), $brand);
		}

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$marque = $em->getRepository('AppBundle:KpiWeek')->getKpiMarque($dateWeek3, $dateWeek2, $user->getUsername());
		}
		else{
			$marque = $em->getRepository('AppBundle:KpiWeek')->getKpiMarque($dateWeek3, $dateWeek2, $user->getBrand());
		}

		
    if ( $request->getMethod() == 'POST' && $form->isSubmitted() ) {
		//Mise à jour des variable de session
		$kpiFilterService->updateSessionVars($data);
		$reseau    = $session->get('filtre_reseau');
		$dr 	   = $session->get('filtre_dr');
		$boutique  = $session->get('filtre_boutique');
		$vendeur   = $session->get('filtre_vendeur');


		$datesWeek = $kpiDates->getDatesWeekPost($data, $session, 0);
	  	$week 		= $datesWeek['week'];
		$weekYear	= $datesWeek['year'];
		$dateWeek1 	= $datesWeek['dateWeek1'];
		$dateWeek2 	= $datesWeek['dateWeek2'];
		$dateWeek3 	= $datesWeek['dateWeek3'];


		if($session->get('filtre_vendeur') != null){
			$id = $session->get('filtre_vendeur')->getId();
	    }
	    elseif($session->get('filtre_boutique') != null){
				$id = $session->get('filtre_boutique')->getId();
	    }
		elseif($session->get('filtre_dr') != null){
			$id = $session->get('filtre_dr')->getId();
		}
		elseif($session->get('filtre_reseau') != null){
			$id = $session->get('filtre_reseau')->getId();
		}
		else{
			$id = $user->getId();
		}


		if($routeName == "app_kpi_week"){
			return $this->redirectToRoute('app_kpi_week', array('user_actuel' => $user_actuel->getId(),'user_id' =>$id));
		}
    }

  //Gestion des requêtes selon la page appelée

	if($session->get('filtre_boutique') != null){
		$kpis = $em->getRepository('AppBundle:KpiWeek')->getUserKpisBetweenDates($session->get('filtre_boutique'), $dateWeek1, $dateWeek2, $brand);
	}
	elseif($session->get('filtre_dr') != null){
		$kpis = $em->getRepository('AppBundle:KpiWeek')->getUserKpisBetweenDates($session->get('filtre_dr'), $dateWeek1, $dateWeek2, $brand);
	}
	elseif($session->get('filtre_reseau') != null){
		$kpis = $em->getRepository('AppBundle:KpiWeek')->getUserKpisBetweenDates($session->get('filtre_reseau'), $dateWeek1, $dateWeek2, $brand);
	}
	else{
		$kpis = $em->getRepository('AppBundle:KpiWeek')->getUserKpisBetweenDates($user, $dateWeek1, $dateWeek2, $brand);
	}

	$kpiCurrentWeek = null;

	//get current month depending on url parameter
	foreach ($kpis as $key => $kpi) {
		if ( $week == null ) {
			if ( $key == 0 )
				$kpiCurrentWeek = $kpi;
				$week = $kpiCurrentWeek->getDate()->format("W");
		}
		else {
			if ( $kpi->getDate()->format("W") == $week && $kpi->getDate()->format("Y") == $year ) {
				$kpiCurrentWeek = $kpi;
			}
		}
	}

	if ($kpis == null or $kpiCurrentWeek == null){
		//throw new NotFoundHttpException("No data Available");
		//$kpiCurrentWeek = $lastKpiWeek;
		$session->remove('kpi_month_filtre');
    	$session->remove('kpi_year_filtre');
    	$session->remove('kpi_week_filtre');

		//return $this->redirectToRoute('app_kpi_week', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$user->getId()));
	}

	//Récupération des top
	if($routeName == "app_kpi_week"){
		$topNpe = $em->getRepository('AppBundle:KpiWeek')->getRank1Npe($dateWeek3, $dateWeek2, $brand);
		$topNpes = $em->getRepository('AppBundle:KpiWeek')->getRank1Npes($dateWeek3, $dateWeek2, $brand);
		$topNpesa = $em->getRepository('AppBundle:KpiWeek')->getRank1Npesa($dateWeek3, $dateWeek2, $brand);

		$topNpeVendeur = $em->getRepository('AppBundle:KpiWeek')->getRank1NpeVendeur($dateWeek3, $dateWeek2, $brand);
		$topNpesVendeur = $em->getRepository('AppBundle:KpiWeek')->getRank1NpesVendeur($dateWeek3, $dateWeek2, $brand);
		$topNpesaVendeur = $em->getRepository('AppBundle:KpiWeek')->getRank1NpesaVendeur($dateWeek3, $dateWeek2, $brand);

		$topNpe2 = $em->getRepository('AppBundle:KpiWeek')->getRank1Npe2($dateWeek3, $dateWeek2, $brand);
		$topNps2 = $em->getRepository('AppBundle:KpiWeek')->getRank1Nps2($dateWeek3, $dateWeek2, $brand);
		$topNpes2 = $em->getRepository('AppBundle:KpiWeek')->getRank1Npes2($dateWeek3, $dateWeek2, $brand);

		$topNpeVendeur2 = $em->getRepository('AppBundle:KpiWeek')->getRank1Npe2Vendeur($dateWeek3, $dateWeek2, $brand, $vendeurBoutique);
		$topNpsVendeur2 = $em->getRepository('AppBundle:KpiWeek')->getRank1Nps2Vendeur($dateWeek3, $dateWeek2, $brand, $vendeurBoutique);
		$topNpes2Vendeur2 = $em->getRepository('AppBundle:KpiWeek')->getRank1Npes2Vendeur($dateWeek3, $dateWeek2, $brand, $vendeurBoutique);
	}

	//Mise à jour du filtre
	$form = $kpiFilterService->updateForm($user, $request, $form);

	$form2 = $this->createForm(new ExportDataType());
  	$form2->handleRequest($request);

		//Export CSV
		if ($form2->isSubmitted()) {

			$ids = "(";

			foreach ($kpisCSV as $key => $id_kpi){
				if($key == 0){
					$ids .= $id_kpi['id'];
				}
				else{
					$ids .= ",".$id_kpi['id'];
				}
			}
			$ids .= ")";

			if($kpiCurrentWeek->getDate() < new \Datetime('2018-12-31') || $request->get('old') == t)
			{
				$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_S0,d.tx_transac_linked_S0,d.tx_transac_npe_S0,d.tx_transac_npes_S0,d.tx_transac_npesa_S0
						FROM app_kpi_week d
						LEFT JOIN fos_user_user u on d.user_id = u.id
						WHERE d.id in $ids
						ORDER BY d.date DESC, u.role";
				$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS Hebdomadaire',
		            				'TAUX DE TRANSACTIONS LIÉES Hebdomadaire','CAPTURE EMAIL VALIDE Hebdomadaire','CAPTURE EMAIL + SMS VALIDE Hebdomadaire','CAPTURE EMAIL + SMS + ADRESSE VALIDE Hebdomadaire');

				//Creation du fichier CSV et du header
	            $handle     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle, $header, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt = $pdo->prepare($sql);
	            $stmt->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	                $row["nb_transac_S0"] = str_replace('.',',',$row["nb_transac_S0"]);
	                $row["tx_transac_linked_S0"] = str_replace('.',',',$row["tx_transac_linked_S0"]);
	                $row["tx_transac_npe_S0"] = str_replace('.',',',$row["tx_transac_npe_S0"]);
	                $row["tx_transac_npes_S0"]  = str_replace('.',',',$row["tx_transac_npes_S0"]);
	                $row["tx_transac_npesa_S0"]  = str_replace('.',',',$row["tx_transac_npesa_S0"]);
	                fputcsv($handle, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle);
	            $content = stream_get_contents($handle);
	            fclose($handle);
	        }
	        else{
	        	$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_s0,d.tx_transac_linked_s0,d.tx_transac_npesi2_s0,d.tx_transac_npei_s0,d.tx_transac_npsi_s0
						FROM app_kpi_week d
						LEFT JOIN fos_user_user u on d.user_id = u.id
						WHERE d.id in $ids
						ORDER BY d.date DESC, u.role";
				$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS Hebdo',
	            					'TAUX DE TRANSACTIONS LIÉES Hebdo','CAPTURE EMAIL ET/OU SMS VALIDE et OPTIN Hebdo','CAPTURE EMAIL VALIDE et OPTIN Hebdo','CAPTURE SMS VALIDE et OPTIN Hebdo');

				//Creation du fichier CSV et du header
	            $handle     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle, $header, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt = $pdo->prepare($sql);
	            $stmt->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	                $row["nb_transac_s0"] = str_replace('.',',',$row["nb_transac_s0"]);
	                $row["tx_transac_linked_s0"] = str_replace('.',',',$row["tx_transac_linked_s0"]);
	                $row["tx_transac_npesi2_s0"] = str_replace('.',',',$row["tx_transac_npesi2_s0"]);
	                $row["tx_transac_npei_s0"]  = str_replace('.',',',$row["tx_transac_npei_s0"]);
	                $row["tx_transac_npsi_s0"]  = str_replace('.',',',$row["tx_transac_npsi_s0"]);
	                fputcsv($handle, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle);
	            $content = stream_get_contents($handle);
	            fclose($handle);

	        }

            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            //Reponse : Téléchargement du fichier
            return new Response($content, 200, array(
                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="export_donnees_'.$date_csv.'.csv"'
            ));

        }

        if($routeName == "app_kpi_satisfaction_week"){
	        //Mise à jour du filtre
			//$kpiFilterService->updateFormVerbatime($user, $request);

			$formMontre = $this->createForm(new ExportVerbatimType(1));
	        $formMontre->handleRequest($request);

			$formMontreAll = $this->createForm(new ExportVerbatimType(2));
	        $formMontreAll->handleRequest($request);

			$formPile = $this->createForm(new ExportVerbatimType(3));
	        $formPile->handleRequest($request);

			$formPileAll = $this->createForm(new ExportVerbatimType(4));
	        $formPileAll->handleRequest($request);

			$formRank = $this->createForm(new ExportVerbatimType(5));
	        $formRank->handleRequest($request);

			//Export Verbatim CSV

			if ($formMontre->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
			}

			if ($formMontreAll->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Montre'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Montre'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Montre'";
				}
			}

			if ($formPile->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
			}

			if ($formPileAll->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Pile/Bracelet'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Pile/Bracelet'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Pile/Bracelet'";
				}
			}


			$header2     = array('Reseau','DR','Boutique','Type','Question','Note','Verbatim','Date de validation du questionnaire');

			if($formMontre->isSubmitted() OR $formMontreAll->isSubmitted() OR $formPile->isSubmitted() OR $formPileAll->isSubmitted()){
	            //Creation du fichier CSV et du header2
	            $handle2     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle2, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle2, $header2, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt2 = $pdo->prepare($sql2);

	            $stmt2->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt2->fetch(\PDO::FETCH_ASSOC)) {
	                $row["note"] = str_replace('.',',',$row["note"]);

	                fputcsv($handle2, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle2);
	            $content = stream_get_contents($handle2);
	            fclose($handle2);
	        }

            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            if ($formPileAll->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_pile_bracelet_histo_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formMontreAll->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_montre_histo_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formPile->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_pile_bracelet_'.str_replace(' ','_',$username).'_mois_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formMontre->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_montre_mois_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }

			if ($formRank->isSubmitted()) {

				$username = addslashes($user->getUsername());
				$reseau = $user->getBrand();


				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_s0, k.quest_satisf_nps_s0, k.nbre_questsatisf_s0
						FROM app_kpi_week k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$username' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_s0, k.quest_satisf_nps_s0, k.nbre_questsatisf_s0
						FROM app_kpi_week k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$reseau' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_s0, k.quest_satisf_nps_s0, k.nbre_questsatisf_s0
						FROM app_kpi_week k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$reseau' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}

				$header3     = array('DR','Boutique','Semaine','Classement de la semaine','Note NPS de la semaine','Nombre de répondants sur la semaine');

		            //Creation du fichier CSV et du header2
		            $handle3     = fopen('php://memory', 'r+');

		            //Creation de l'entête du fichier pour être lisible dans Exel
		            fputs($handle3, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		            fputcsv($handle3, $header3, ';');

		            //Initialisation de la connection à la BDD
		            $pdo = $this->container->get('app.pdo_connect');
		            $pdo = $pdo->initPdoClienteling();

		            //Préparation et execution de la requête
		            $stmt3 = $pdo->prepare($sql3);
		            $stmt3->execute();

		            //Remplissage du fichier csv.
		            while ($row = $stmt3->fetch(\PDO::FETCH_ASSOC)) {
		            	$date_format = $row["date"];
		            	$date_format = new \Datetime($date_format);
		            	$date_format = $date_format->format('m-Y');
                		$row["date"] = $date_format;
                		$row["quest_satisf_nps_s0"] = round($row["quest_satisf_nps_s0"]);
		                fputcsv($handle3, $row, ';');
		            }

		            //Fermeture du fichier
		            rewind($handle3);
		            $content = stream_get_contents($handle3);
		            fclose($handle3);

	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_classement_'.$date_format.'.csv"'
	            ));

			}
		}

        if($kpiCurrentWeek != null) {
        
	        if($kpiCurrentWeek->getDate() < new \Datetime('2018-12-31') || $request->get('old') == t)
			{
				$path_week = 'AppBundle:Kpi:week.html.twig';
			}
			else{
				$path_week = 'AppBundle:Kpi:week_2019.html.twig';
			}

        }
        else {
        	$path_week = 'AppBundle:Kpi:week_2019.html.twig';
        }

		//Retourne la bonne page
		if($routeName == "app_kpi_week"){
	        return $this->render($path_week, array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentWeek,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'topNpeVendeur'		=> $topNpeVendeur,
	        	'topNpesVendeur'	=> $topNpesVendeur,
	        	'topNpesaVendeur'	=> $topNpesaVendeur,
	        	'topNpe2'			=> $topNpe2,
	        	'topNps2'			=> $topNps2,
	        	'topNpes2'			=> $topNpes2,
	        	'topNpeVendeur2'	=> $topNpeVendeur2,
	        	'topNpsVendeur2'	=> $topNpsVendeur2,
	        	'topNpesVendeur2'	=> $topNpeVendeur2,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'user_actuel'		=> $user_actuel,
	        	'user_role'			=> $user->getRole()
	        	)
	        );
		}
	    if($routeName == "app_kpi_satisfaction_week"){
	        return $this->render('AppBundle:Kpi:satisfaction_week.html.twig', array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentWeek,
	        	'topNPS'			=> $topNPS,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'formMontre'       	=> $formMontre->createView(),
	        	'formPile'        	=> $formPile->createView(),
	        	'formMontreAll'    	=> $formMontreAll->createView(),
	        	'formPileAll'       => $formPileAll->createView(),
	        	'formRank'      	=> $formRank->createView(),
	        	'scope'				=> "hebdomadaire",
	        	'user_bis'			=> $user_id,
	        	'user_actuel'		=> $user_actuel,
	        	'user_role'			=> $user->getRole()
	        	)
	        );
		}
	}

	/**
	 * @ParamConverter("user_actuel", options={"mapping": {"user_actuel": "id"}})
     */
	public function kpiTrimAction(User $user_actuel, $user_id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();
		$routeName = $request->get('_route');

		//$session->clear();

		if($user_id == 0) {$user = $user_actuel;}
		else {$user = $em->getRepository('ApplicationSonataUserBundle:User')->findOneById($user_id);}

		$session->remove('filtre_reseau');
		$session->remove('filtre_dr');
		$session->remove('filtre_boutique');
		$session->remove('filtre_vendeur');

		$lastKpiTrim = $em->getRepository('AppBundle:KpiTrim')->findOneBy(array('user' => $user), array('date' => "DESC"));

		if($lastKpiTrim == null){
			$session->remove('kpi_trim_filtre');
			$session->remove('kpi_year_filtre');

			return $this->render('AppBundle:Kpi:no_data.html.twig', array(
	        	'user'				=> $user,
	        	)
	        );
		}

		// ATTENTION FAIRE UNE PAGE NO DATA POUR DES RESULTATS NULL
		$dateTrim = $lastKpiTrim->getDate();

		//initialisation des variable de session
		$kpiFilterService = $this->container->get('app.kpi_filter_session');
		$vars = $kpiFilterService->initVars($user, $request);

        $reseau      = $vars[0];
        $dr 		 = $vars[1];
        $boutique    = $vars[2];
        $vendeur     = $vars[3];


        //simplification du code par utilisation d'un service pour initialiser les dates utiliser pour filtrer des données
		$kpiDates = $this->get('app.init_Kpi_dates');
		$datesTrim = $kpiDates->getDatesTrim($dateTrim, $session, 0);

		$trim 		= $datesTrim['trim'];
		$year 		= $datesTrim['year'];
		$dateTrim1 	= $datesTrim['dateTrim1'];//Premier jour du mois à J - X mois
		$dateTrim2 	= $datesTrim['dateTrim2'];//Dernier jour du mois
		$dateTrim3 	= $datesTrim['dateTrim3'];//Premier jour du mois

		if($session->get('kpi_trim_filtre') != null){
			$form = $this->createForm(new KpiFilterType($em, $user, $user, null, null, $session->get('kpi_trim_filtre'), $session->get('kpi_year_filtre') , 'trimestre'));
		}
		else{
			$form = $this->createForm(new KpiFilterType($em, $user, $user, null, null, $trim, $year, 'trimestre'));
		}


		$form->handleRequest($request);
		//Recuperation des données de la requete
        $data = $form->getData();

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';


		$vendeurBoutique = $user->getBoutique();
		if ($vendeurBoutique == null) $vendeurBoutique = '';

		$getBoutiquesDr = null;
		$getDrsMarque = null;

		//Requetes Mensuelles / hebdomadaire
		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$getDrsMarque = $em->getRepository('AppBundle:KpiTrim')->getKpiDrMarque($dateTrim3, $dateTrim2, $brand);
			$getBoutiquesDr = array();
			$getVendeursBoutique = null;

			foreach ($getDrsMarque as $key => $dr) {
				$getBoutiques = $em->getRepository('AppBundle:KpiTrim')->getKpiBoutiqueDr($dr->getUser()->getUsername(), $dateTrim3, $dateTrim2, $brand);
				$getBoutiquesDr[$key] = $getBoutiques;
			}

			$kpisCSV = $em->getRepository('AppBundle:KpiTrim')->getKpisMarque($dateTrim1, $dateTrim2, $brand);
		}
		if( $user->getRole() == 'ROLE_DR' ) {
			$getBoutiquesDr = $em->getRepository('AppBundle:KpiTrim')->getKpiBoutiqueDr($user->getUsername(), $dateTrim3, $dateTrim2, $brand);
			$getVendeursBoutique = array();
			$getDrsMarque = null;

			foreach ($getBoutiquesDr as $key2 => $boutique) {
				$getVendeurs = $em->getRepository('AppBundle:KpiTrim')->getKpiVendeurBoutique($boutique->getUser()->getUsername(), $dateTrim3, $dateTrim2, $brand);
				$getVendeursBoutique[$key2] = $getVendeurs;
			}

			$kpisCSV = $em->getRepository('AppBundle:KpiTrim')->getKpisDr($dateTrim1, $dateTrim2, $user->getUsername(), $brand);
		}
		if( $user->getRole() == 'ROLE_BOUTIQUE' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiTrim')->getKpiVendeurBoutique($user->getUsername(), $dateTrim3, $dateTrim2, $brand);
			$getBoutiquesDr = null;
			$getDrsMarque = null;

			$kpisCSV = $em->getRepository('AppBundle:KpiTrim')->getKpisBoutique($dateTrim1, $dateTrim2, $user->getBoutique(), $brand);
		}
		if( $user->getRole() == 'ROLE_VENDEUR' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiTrim')->getKpiVendeurBoutique($user->getBoutique(), $dateTrim3, $dateTrim2, $brand);
			$getBoutiquesDr = null;
			$getDrsMarque = null;

			$kpisCSV = $em->getRepository('AppBundle:KpiTrim')->getKpisBoutique($dateTrim1, $dateTrim2, $user->getBoutique(), $brand);
		}

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$marque = $em->getRepository('AppBundle:KpiTrim')->getKpiMarque($dateTrim3, $dateTrim2, $user->getUsername());
		}
		else{
			$marque = $em->getRepository('AppBundle:KpiTrim')->getKpiMarque($dateTrim3, $dateTrim2, $user->getBrand());
		}

		
    if ( $request->getMethod() == 'POST' && $form->isSubmitted() ) {
		//Mise à jour des variable de session
		$kpiFilterService->updateSessionVars($data);
		$reseau    = $session->get('filtre_reseau');
		$dr 	   = $session->get('filtre_dr');
		$boutique  = $session->get('filtre_boutique');
		$vendeur   = $session->get('filtre_vendeur');


		$datesTrim = $kpiDates->getDatesTrimPost($data, $session, 0);
	  	$trim 		= $datesTrim['trim'];
		$trimYear	= $datesTrim['year'];
		$dateTrim1 	= $datesTrim['dateTrim1'];
		$dateTrim2 	= $datesTrim['dateTrim2'];
		$dateTrim3 	= $datesTrim['dateTrim3'];

		if($session->get('filtre_vendeur') != null){
			$id = $session->get('filtre_vendeur')->getId();
	    }
	    elseif($session->get('filtre_boutique') != null){
				$id = $session->get('filtre_boutique')->getId();
	    }
		elseif($session->get('filtre_dr') != null){
			$id = $session->get('filtre_dr')->getId();
		}
		elseif($session->get('filtre_reseau') != null){
			$id = $session->get('filtre_reseau')->getId();
		}
		else{
			$id = $user->getId();
		}


		if($routeName == "app_kpi_trim"){
			return $this->redirectToRoute('app_kpi_trim', array('user_actuel' => $user_actuel->getId(),'user_id' =>$id));
		}
    }

  //Gestion des requêtes selon la page appelée

	if($session->get('filtre_boutique') != null){
		$kpis = $em->getRepository('AppBundle:KpiTrim')->getUserKpisBetweenDates($session->get('filtre_boutique'), $dateTrim1, $dateTrim2, $brand);
	}
	elseif($session->get('filtre_dr') != null){
		$kpis = $em->getRepository('AppBundle:KpiTrim')->getUserKpisBetweenDates($session->get('filtre_dr'), $dateTrim1, $dateTrim2, $brand);
	}
	elseif($session->get('filtre_reseau') != null){
		$kpis = $em->getRepository('AppBundle:KpiTrim')->getUserKpisBetweenDates($session->get('filtre_reseau'), $dateTrim1, $dateTrim2, $brand);
	}
	else{
		$kpis = $em->getRepository('AppBundle:KpiTrim')->getUserKpisBetweenDates($user, $dateTrim1, $dateTrim2, $brand);
	}

	$kpiCurrentTrim = null;

	//get current month depending on url parameter
	foreach ($kpis as $key => $kpi) {
		if ( $trim == null ) {
			if ( $key == 0 )
			$kpiCurrentTrim = $kpi;
			$month = $kpiCurrentTrim->getDate()->format("n");
        	$trim = floor(($month-1)/3)+1;
		}
		else {
			$month = $kpi->getDate()->format("n");
        	$trim2 = floor(($month-1)/3)+1;
			if ( $trim2 == $trim && $kpi->getDate()->format("Y") == $year ) {
				$kpiCurrentTrim = $kpi;
			}
		}
	}

	if ($kpis == null or $kpiCurrentTrim == null){
		//throw new NotFoundHttpException("No data Available");
		//$kpiCurrentTrim = $lastKpiTrim;
		$session->remove('kpi_month_filtre');
    	$session->remove('kpi_year_filtre');
    	$session->remove('kpi_trim_filtre');
    	$session->remove('kpi_week_filtre');

		//return $this->redirectToRoute('app_kpi_trim', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$user->getId()));
	}

	//Récupération des top
	if($routeName == "app_kpi_trim"){
		$topNpe = $em->getRepository('AppBundle:KpiTrim')->getRank1Npe($dateTrim3, $dateTrim2, $brand);
		$topNpes = $em->getRepository('AppBundle:KpiTrim')->getRank1Npes($dateTrim3, $dateTrim2, $brand);
		$topNpesa = $em->getRepository('AppBundle:KpiTrim')->getRank1Npesa($dateTrim3, $dateTrim2, $brand);

		$topNpeVendeur = $em->getRepository('AppBundle:KpiTrim')->getRank1NpeVendeur($dateTrim3, $dateTrim2, $brand);
		$topNpesVendeur = $em->getRepository('AppBundle:KpiTrim')->getRank1NpesVendeur($dateTrim3, $dateTrim2, $brand);
		$topNpesaVendeur = $em->getRepository('AppBundle:KpiTrim')->getRank1NpesaVendeur($dateTrim3, $dateTrim2, $brand);

		$topNpe2 = $em->getRepository('AppBundle:KpiTrim')->getRank1Npe2($dateTrim3, $dateTrim2, $brand);
		$topNps2 = $em->getRepository('AppBundle:KpiTrim')->getRank1Nps2($dateTrim3, $dateTrim2, $brand);
		$topNpes2 = $em->getRepository('AppBundle:KpiTrim')->getRank1Npes2($dateTrim3, $dateTrim2, $brand);

		$topNpeVendeur2 = $em->getRepository('AppBundle:KpiTrim')->getRank1Npe2Vendeur($dateTrim3, $dateTrim2, $brand, $vendeurBoutique);
		$topNpsVendeur2 = $em->getRepository('AppBundle:KpiTrim')->getRank1Nps2Vendeur($dateTrim3, $dateTrim2, $brand, $vendeurBoutique);
		$topNpes2Vendeur2 = $em->getRepository('AppBundle:KpiTrim')->getRank1Npes2Vendeur($dateTrim3, $dateTrim2, $brand, $vendeurBoutique);
	}

	//Mise à jour du filtre
	$form = $kpiFilterService->updateForm($user, $request, $form);

	$form2 = $this->createForm(new ExportDataType());
  	$form2->handleRequest($request);

		//Export CSV
		if ($form2->isSubmitted()) {
			
			$ids = "(";

			foreach ($kpisCSV as $key => $id_kpi){
				if($key == 0){
					$ids .= $id_kpi['id'];
				}
				else{
					$ids .= ",".$id_kpi['id'];
				}
			}
			$ids .= ")";

			if($kpiCurrentTrim->getDate() < new \Datetime('2018-12-31') || $request->get('old') == t)
			{
				$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_T0,d.tx_transac_linked_T0,d.tx_transac_npe_T0,d.tx_transac_npes_T0,d.tx_transac_npesa_T0
						FROM app_kpi_trim d
						LEFT JOIN fos_user_user u on d.user_id = u.id
						WHERE d.id in $ids
						ORDER BY d.date DESC, u.role";
				$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS Hebdomadaire',
		            				'TAUX DE TRANSACTIONS LIÉES Hebdomadaire','CAPTURE EMAIL VALIDE Hebdomadaire','CAPTURE EMAIL + SMS VALIDE Hebdomadaire','CAPTURE EMAIL + SMS + ADRESSE VALIDE Hebdomadaire');

				//Creation du fichier CSV et du header
	            $handle     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle, $header, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt = $pdo->prepare($sql);
	            $stmt->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	                $row["nb_transac_T0"] = str_replace('.',',',$row["nb_transac_T0"]);
	                $row["tx_transac_linked_T0"] = str_replace('.',',',$row["tx_transac_linked_T0"]);
	                $row["tx_transac_npe_T0"] = str_replace('.',',',$row["tx_transac_npe_T0"]);
	                $row["tx_transac_npes_T0"]  = str_replace('.',',',$row["tx_transac_npes_T0"]);
	                $row["tx_transac_npesa_T0"]  = str_replace('.',',',$row["tx_transac_npesa_T0"]);
	                fputcsv($handle, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle);
	            $content = stream_get_contents($handle);
	            fclose($handle);
	        }
	        else{
	        	$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_T0,d.tx_transac_linked_T0,d.tx_transac_npesi2_T0,d.tx_transac_npei_T0,d.tx_transac_npsi_T0
						FROM app_kpi_trim d
						LEFT JOIN fos_user_user u on d.user_id = u.id
						WHERE d.id in $ids
						ORDER BY d.date DESC, u.role";
				$header     = array('Libelle','Role','Reseau','DR','Boutique','Nom Vendeur','Prenom Vendeur','Date','NOMBRE DE TRANSACTIONS Hebdo',
	            					'TAUX DE TRANSACTIONS LIÉES Hebdo','CAPTURE EMAIL ET/OU SMS VALIDE et OPTIN Hebdo','CAPTURE EMAIL VALIDE et OPTIN Hebdo','CAPTURE SMS VALIDE et OPTIN Hebdo');

				//Creation du fichier CSV et du header
	            $handle     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle, $header, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt = $pdo->prepare($sql);
	            $stmt->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
	                $row["nb_transac_T0"] = str_replace('.',',',$row["nb_transac_T0"]);
	                $row["tx_transac_linked_T0"] = str_replace('.',',',$row["tx_transac_linked_T0"]);
	                $row["tx_transac_npesi2_T0"] = str_replace('.',',',$row["tx_transac_npesi2_T0"]);
	                $row["tx_transac_npei_T0"]  = str_replace('.',',',$row["tx_transac_npei_T0"]);
	                $row["tx_transac_npsi_T0"]  = str_replace('.',',',$row["tx_transac_npsi_T0"]);
	                fputcsv($handle, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle);
	            $content = stream_get_contents($handle);
	            fclose($handle);

	        }

            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            //Reponse : Téléchargement du fichier
            return new Response($content, 200, array(
                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="export_donnees_'.$date_csv.'.csv"'
            ));

        }

        if($routeName == "app_kpi_satisfaction_trim"){
	        //Mise à jour du filtre
			//$kpiFilterService->updateFormVerbatime($user, $request);

			$formMontre = $this->createForm(new ExportVerbatimType(1));
	        $formMontre->handleRequest($request);

			$formMontreAll = $this->createForm(new ExportVerbatimType(2));
	        $formMontreAll->handleRequest($request);

			$formPile = $this->createForm(new ExportVerbatimType(3));
	        $formPile->handleRequest($request);

			$formPileAll = $this->createForm(new ExportVerbatimType(4));
	        $formPileAll->handleRequest($request);

			$formRank = $this->createForm(new ExportVerbatimType(5));
	        $formRank->handleRequest($request);

			//Export Verbatim CSV

			if ($formMontre->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Montre' and v.date BETWEEN '$date3' and '$date2'";
				}
			}

			if ($formMontreAll->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Montre'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Montre'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Montre'";
				}
			}

			if ($formPile->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Pile/Bracelet' and v.date BETWEEN '$date3' and '$date2'";
				}
			}

			if ($formPileAll->isSubmitted()) {

				$username = addslashes($user->getUsername());

				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.marque = '$username' and v.type = 'Pile/Bracelet'";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.dr = '$username' and v.type = 'Pile/Bracelet'";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql2 = "SELECT v.marque,v.dr,v.boutique,v.type,v.question,v.note,v.verbatim,v.date
						FROM app_verbatim v
						WHERE v.boutique = '$username' and v.type = 'Pile/Bracelet'";
				}
			}


			$header2     = array('Reseau','DR','Boutique','Type','Question','Note','Verbatim','Date de validation du questionnaire');

			if($formMontre->isSubmitted() OR $formMontreAll->isSubmitted() OR $formPile->isSubmitted() OR $formPileAll->isSubmitted()){
	            //Creation du fichier CSV et du header2
	            $handle2     = fopen('php://memory', 'r+');

	            //Creation de l'entête du fichier pour être lisible dans Exel
	            fputs($handle2, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
	            fputcsv($handle2, $header2, ';');

	            //Initialisation de la connection à la BDD
	            $pdo = $this->container->get('app.pdo_connect');
	            $pdo = $pdo->initPdoClienteling();

	            //Préparation et execution de la requête
	            $stmt2 = $pdo->prepare($sql2);

	            $stmt2->execute();

	            //Remplissage du fichier csv.
	            while ($row = $stmt2->fetch(\PDO::FETCH_ASSOC)) {
	                $row["note"] = str_replace('.',',',$row["note"]);

	                fputcsv($handle2, $row, ';');
	            }

	            //Fermeture du fichier
	            rewind($handle2);
	            $content = stream_get_contents($handle2);
	            fclose($handle2);
	        }

            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            if ($formPileAll->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_pile_bracelet_histo_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formMontreAll->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_montre_histo_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formPile->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_pile_bracelet_'.str_replace(' ','_',$username).'_mois_'.$date_csv.'.csv"'
	            ));
	        }
	        if ($formMontre->isSubmitted()) {
	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_verbatims_montre_mois_'.str_replace(' ','_',$username).'_'.$date_csv.'.csv"'
	            ));
	        }

			if ($formRank->isSubmitted()) {

				$username = addslashes($user->getUsername());
				$reseau = $user->getBrand();


				if( $user->getRole() == 'ROLE_MARQUE' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_t0, k.quest_satisf_nps_t0, k.nbre_questsatisf_t0
						FROM app_kpi_trim k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$username' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}
				elseif( $user->getRole() == 'ROLE_DR' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_t0, k.quest_satisf_nps_t0, k.nbre_questsatisf_t0
						FROM app_kpi_trim k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$reseau' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}
				elseif( $user->getRole() == 'ROLE_BOUTIQUE' ) {
					$sql3 = "SELECT u.dr, u.boutique, k.date, k.quest_satisf_rank_nps_t0, k.quest_satisf_nps_t0, k.nbre_questsatisf_t0
						FROM app_kpi_trim k
						LEFT JOIN fos_user_user u on k.user_id = u.id
						WHERE u.brand = '$reseau' and u.role = 'ROLE_BOUTIQUE' and k.date BETWEEN '$date3' and '$date2'
						ORDER BY k.quest_satisf_rank_nps_ytd";
				}

				$header3     = array('DR','Boutique','Trimestre','Classement du trimestre','Note NPS du trimestre','Nombre de répondants sur le trimestre');

		            //Creation du fichier CSV et du header2
		            $handle3     = fopen('php://memory', 'r+');

		            //Creation de l'entête du fichier pour être lisible dans Exel
		            fputs($handle3, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		            fputcsv($handle3, $header3, ';');

		            //Initialisation de la connection à la BDD
		            $pdo = $this->container->get('app.pdo_connect');
		            $pdo = $pdo->initPdoClienteling();

		            //Préparation et execution de la requête
		            $stmt3 = $pdo->prepare($sql3);
		            $stmt3->execute();

		            //Remplissage du fichier csv.
		            while ($row = $stmt3->fetch(\PDO::FETCH_ASSOC)) {
		            	$date_format = $row["date"];
		            	$date_format = new \Datetime($date_format);
		            	$date_format = $date_format->format('m-Y');
                		$row["date"] = $date_format;
                		$row["quest_satisf_nps_t0"] = round($row["quest_satisf_nps_t0"]);
		                fputcsv($handle3, $row, ';');
		            }

		            //Fermeture du fichier
		            rewind($handle3);
		            $content = stream_get_contents($handle3);
		            fclose($handle3);

	            //Reponse : Téléchargement du fichier
	            return new Response($content, 200, array(
	                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
	                'Content-Disposition' => 'attachment; filename="export_classement_'.$date_format.'.csv"'
	            ));

			}
		}

       	$path_trim = 'AppBundle:Kpi:trim_2019.html.twig';

		//Retourne la bonne page
		if($routeName == "app_kpi_trim"){
	        return $this->render($path_trim, array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentTrim,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'topNpeVendeur'		=> $topNpeVendeur,
	        	'topNpesVendeur'	=> $topNpesVendeur,
	        	'topNpesaVendeur'	=> $topNpesaVendeur,
	        	'topNpe2'			=> $topNpe2,
	        	'topNps2'			=> $topNps2,
	        	'topNpes2'			=> $topNpes2,
	        	'topNpeVendeur2'	=> $topNpeVendeur2,
	        	'topNpsVendeur2'	=> $topNpsVendeur2,
	        	'topNpesVendeur2'	=> $topNpeVendeur2,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'user_actuel'		=> $user_actuel,
	        	'user_role'			=> $user->getRole()
	        	)
	        );
		}

	    if($routeName == "app_kpi_satisfaction_trim"){
	        return $this->render('AppBundle:Kpi:satisfaction_trim.html.twig', array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentTrim,
	        	'topNPS'			=> $topNPS,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'formMontre'       	=> $formMontre->createView(),
	        	'formPile'        	=> $formPile->createView(),
	        	'formMontreAll'    	=> $formMontreAll->createView(),
	        	'formPileAll'       => $formPileAll->createView(),
	        	'formRank'      	=> $formRank->createView(),
	        	'scope'				=> "trimestre",
	        	'user_bis'			=> $user_id,
	        	'user_actuel'		=> $user_actuel,
	        	'user_role'			=> $user->getRole()
	        	)
	        );
		}
	}

	/**
	 * @ParamConverter("user_actuel", options={"mapping": {"user_actuel": "id"}})
     */

	public function faqAction(User $user_actuel, $user_id, Request $request){
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();

		if($user_id == 0) {$user = $user_actuel;}
		else {$user = $em->getRepository('ApplicationSonataUserBundle:User')->findOneById($user_id);}

		return $this->render('AppBundle:Kpi:faq.html.twig', array(
        	'user'				=> $user,
        	'user_actuel'		=> $user_actuel,
        	)
        );
	}

	/**
     * @ParamConverter("user", options={"mapping": {"user_actuel": "id"}})
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
	public function ajaxfilterAction(User $user_actuel, User $user, $scope, $week, $month, $trim, $year, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();

		$form = $this->createForm(new KpiFilterType($em, $user_actuel,$user, $week, $month, $trim, $year, $scope));

		$role = $user->getRole();

		switch($role){
			case "ROLE_MARQUE":
				$reseau = $user;
				$dr = null;
				$boutique = null;
			break;
			case "ROLE_DR":
				$reseau = $em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(array('username' => $user->getBrand()));
				$dr = $user;
				$boutique = null;
			break;
			case "ROLE_BOUTIQUE":
				$reseau = $em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(array('username' => $user->getBrand()));
				$dr = $em->getRepository('ApplicationSonataUserBundle:User')->findOneBy(array('username' => $user->getDr()));
				$boutique = $user;
			break;
		}

		$kpiFilterService = $this->container->get('app.kpi_filter_session');
		$form = $kpiFilterService->updateFormAjax($week, $month, $trim, $year, $reseau, $dr, $boutique, $form);

	    return $this->render('AppBundle:Ajax:ajaxFilter.html.twig', array(
	    	'user'		=> $user_actuel,
        	'form'     	=> $form->createView(),
        	'scope'		=> $scope
	    	)
	    );
	}
}
