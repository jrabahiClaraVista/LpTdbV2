<?php

namespace AppBundle\Controller;


// src/OC/PlatformBundle/Controller/AdvertController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\Sonata\UserBundle\Entity\User as User;
use AppBundle\Entity\KpiWeek;
use AppBundle\Entity\KpiMonth;

use AppBundle\Form\CampaignKpiType;
use AppBundle\Form\KpiFilterType;
use AppBundle\Form\ExportDataType;

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

			if($routeName == "app_kpi_month"){
				if($routeName == "app_kpi_month"){
			        return $this->render('AppBundle:Kpi:no_data.html.twig', array(
			        	'user'				=> $user,
			        	)
			        );
				}
			}
			elseif($routeName == "app_kpi_ytd"){
				if($routeName == "app_kpi_ytd"){
			        return $this->render('AppBundle:Kpi:no_data.html.twig', array(
			        	'user'				=> $user,
			        	)
			        );
				}
			}
			else{
				if($routeName == "app_kpi_fid"){
			        return $this->render('AppBundle:Kpi:no_data.html.twig', array(
			        	'user'				=> $user,
			        	)
			        );
				}
			}
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
			$form = $this->createForm(new KpiFilterType($em, $user, $user, null, $month, $session->get('kpi_year_filtre') , 'mensuel'));
		else
			$form = $this->createForm(new KpiFilterType($em, $user, $user, null, $month, $year, 'mensuel'));

		$form->handleRequest($request);
		//Recuperation des données de la requete
        $data = $form->getData();

        $currentMonth = $lastKpi->getDate()->format("m");
		//$lastKpi 	  = null;


		$brand = $user->getBrand();
		if ($brand == null) $brand = '';

		$getBoutiquesDr = null;
		$getDrsMarque = null;

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$getDrsMarque = $em->getRepository('AppBundle:KpiMonth')->getKpiDrMarque($date3, $date2, $brand);
			$getBoutiquesDr = null;
			$getVendeursBoutique = null;
		}
		if( $user->getRole() == 'ROLE_DR' ) {
			$getBoutiquesDr = $em->getRepository('AppBundle:KpiMonth')->getKpiBoutiqueDr($user->getUsername(), $date3, $date2, $brand);
			$getVendeursBoutique = null;
			$$getDrsMarque = null;
		}
		if( $user->getRole() == 'ROLE_BOUTIQUE' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiMonth')->getKpiVendeurBoutique($user->getUsername(), $date3, $date2, $brand);
			$getBoutiquesDr = null;
			$$getDrsMarque = null;
		}
		if( $user->getRole() == 'ROLE_VENDEUR' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiMonth')->getKpiVendeurBoutique($user->getBoutique(), $date3, $date2, $brand);
			$getBoutiquesDr = null;
			$$getDrsMarque = null;
		}

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$marque = $em->getRepository('AppBundle:KpiMonth')->getKpiMarque($date3, $date2, $user->getUsername());
		}
		else{
			$marque = $em->getRepository('AppBundle:KpiMonth')->getKpiMarque($date3, $date2, $user->getBrand());	
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
		}
		if($routeName == "app_kpi_ytd"){
			$topNpe = $em->getRepository('AppBundle:KpiMonth')->getRank1NpeYtd($date3, $date2, $brand);
			$topNpes = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesYtd($date3, $date2, $brand);
			$topNpesa = $em->getRepository('AppBundle:KpiMonth')->getRank1NpesaYtd($date3, $date2, $brand);
		}
		if($routeName == "app_kpi_fid"){
			$kpisTopCa = $em->getRepository('AppBundle:KpiMonth')->getTop3Ca($brand, $date);
		}
		if($routeName == "app_kpi_planning"){
			$campaigns = $em->getRepository('AppBundle:Campaign')->getCampaignsOfMonth($date3, $date2, $user->getBrand());
			$campaignFile = $em->getRepository('AppBundle:CampaignFile')->getCampaignOfMonth($date3, $date2, $user->getBrand());
		}

		//Mise à jour du filtre
		$kpiFilterService->updateForm($user, $request, $form);

		$form2 = $this->createForm(new ExportDataType());
        $form2->handleRequest($request);  

		//Export CSV
		if ($form2->isSubmitted()) {
			//$id_data = $user->getId();
			$idDataMarque 	= $marque->getId();
			$idDataFiche 	= $kpiCurrentMonth->getId();
			$idDataAutres	= array();
			if( $user->getRole() == 'ROLE_MARQUE' ) {
				foreach ($getDrsMarque as $key => $DrMarque) {
					array_push($idDataAutres, $DrMarque->getId());
				}
			}
			elseif( $user->getRole() == 'ROLE_DR' ) {
				foreach ($getBoutiquesDr as $key => $BoutiqueDr) {
					array_push($idDataAutres, $BoutiqueDr->getId());
				}
			}
			else {
				foreach ($getVendeursBoutique as $key => $VendeurBoutique) {
					array_push($idDataAutres, $VendeurBoutique->getId());
				}
			}

			$ids = "(".$idDataMarque.",".$idDataFiche;

			foreach ($idDataAutres as $key => $id) {
				$ids .= ",".$id;
			}
			$ids .= ")";

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
            //var_dump($sql);
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            //Remplissage du fichier csv.
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            	if($routeName == "app_kpi_month"){
                $row["nb_transac_m0"] = '="'.str_replace('.',',',$row["nb_transac_m0"]).'"';
                $row["tx_transac_linked_m0"] = '="'.str_replace('.',',',$row["tx_transac_linked_m0"]).'"';
                $row["tx_transac_npe_m0"] = '="'.str_replace('.',',',$row["tx_transac_npe_m0"]).'"';
                $row["tx_transac_npes_m0"]  = '="'.str_replace('.',',',$row["tx_transac_npes_m0"]).'"';
                $row["tx_transac_npesa_m0"]  = '="'.str_replace('.',',',$row["tx_transac_npesa_m0"]).'"';
            	}
            	if($routeName == "app_kpi_ytd"){
                $row["nb_transac_ytd"] = '="'.str_replace('.',',',$row["nb_transac_ytd"]).'"';
                $row["tx_transac_linked_ytd"] = '="'.str_replace('.',',',$row["tx_transac_linked_ytd"]).'"';
                $row["tx_transac_npe_ytd"] = '="'.str_replace('.',',',$row["tx_transac_npe_ytd"]).'"';
                $row["tx_transac_npes_ytd"]  = '="'.str_replace('.',',',$row["tx_transac_npes_ytd"]).'"';
                $row["tx_transac_npesa_ytd"]  = '="'.str_replace('.',',',$row["tx_transac_npesa_ytd"]).'"';
            	}
                fputcsv($handle, $row, ';');
            }

            //Fermeture du fichier
            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);
            
            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            //Reponse : Téléchargement du fichier
            return new Response($content, 200, array(
                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="export_donnees_'.$date_csv.'.csv"'
            ));

        }

		//Retourne la bonne page
		if($routeName == "app_kpi_month"){
	        return $this->render('AppBundle:Kpi:month.html.twig', array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentMonth,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'topNpeVendeur'		=> $topNpeVendeur,
	        	'topNpesVendeur'	=> $topNpesVendeur,
	        	'topNpesaVendeur'	=> $topNpesaVendeur,
	        	'currentMonth'		=> $currentMonth,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'scope'				=> 'mensuel',
	        	'user_actuel'		=> $user_actuel
	        	)
	        );
		}
		if($routeName == "app_kpi_ytd"){
	        return $this->render('AppBundle:Kpi:ytd.html.twig', array(
	        	'currentKpi'		=> $kpiCurrentMonth,
	        	'year'				=> $year,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'user'				=> $user,
	        	'month'				=> $month,
	        	'marque'			=> $marque,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'form'          	=> $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'scope'				=> 'mensuel',
	        	'user_actuel'		=> $user_actuel
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
		        'user_actuel'		=> $user_actuel
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

			if($routeName == "app_kpi_week"){
		        return $this->render('AppBundle:Kpi:no_data.html.twig', array(
		        	'user'				=> $user,
		        	)
		        );
			}
		}

		// ATTENTION FAIRE UNE PAGE NO DATA POUR DES RESULTATS NULL
		$dateWeek = $lastKpiWeek->getDate();

		//initialisation des variable de session
		$kpiFilterService = $this->container->get('app.kpi_filter_session');
		$vars = $kpiFilterService->initVars($user, $request);

        $reseau      = $vars[0];
        $dr 	     = $vars[1];
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

		if($session->get('kpi_year_filtre') != null)
			$form = $this->createForm(new KpiFilterType($em, $user, $user, $week, null, $session->get('kpi_year_filtre') , 'hebdomadaire'));
		else
			$form = $this->createForm(new KpiFilterType($em, $user, $user, $week, null, $year, 'hebdomadaire'));
		
		$form->handleRequest($request);
		//Recuperation des données de la requete
        $data = $form->getData();

        $currentWeek  = $lastKpiWeek->getDate()->format("W");
		//$lastKpi 	  = null;

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';

		$getBoutiquesDr = null;
		$getDrsMarque = null;

		//Requetes Mensuelles / hebdomadaire
		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$getDrsMarque = $em->getRepository('AppBundle:KpiWeek')->getKpiDrMarque($dateWeek3, $dateWeek2, $brand);
			$getBoutiquesDr = null;
			$getVendeursBoutique = null;
		}
		if( $user->getRole() == 'ROLE_DR' ) {
			$getBoutiquesDr = $em->getRepository('AppBundle:KpiWeek')->getKpiBoutiqueDr($user->getUsername(), $dateWeek3, $dateWeek2, $brand);
			$getVendeursBoutique = null;
			$$getDrsMarque = null;
		}
		if( $user->getRole() == 'ROLE_BOUTIQUE' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiWeek')->getKpiVendeurBoutique($user->getUsername(), $dateWeek3, $dateWeek2, $brand);
			$getBoutiquesDr = null;
			$$getDrsMarque = null;
		}
		if( $user->getRole() == 'ROLE_VENDEUR' ) {
			$getVendeursBoutique = $em->getRepository('AppBundle:KpiWeek')->getKpiVendeurBoutique($user->getBoutique(), $dateWeek3, $dateWeek2, $brand);
			$getBoutiquesDr = null;
			$$getDrsMarque = null;
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
				//var_dump( $data);
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
		
		
		$kpiCurrentWeek = $kpis[0];

		if ($kpis == null or $kpiCurrentWeek == null){
			//throw new NotFoundHttpException("No data Available");
			//$kpiCurrentWeek = null;
			$session->remove('kpi_month_filtre');
            $session->remove('kpi_year_filtre');
            $session->remove('kpi_week_filtre');

            if($routeName == "app_kpi_month"){
				return $this->redirectToRoute('app_kpi_month', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_ytd"){
				return $this->redirectToRoute('app_kpi_ytd', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_fid"){
				return $this->redirectToRoute('app_kpi_fid', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_planning"){
				return $this->redirectToRoute('app_kpi_planning', array('user_actuel' => $user_actuel->getId(), 'user_id' =>$user->getId()));
			}
		}

		//Récupération des top
		if($routeName == "app_kpi_week"){
			$topNpe = $em->getRepository('AppBundle:KpiWeek')->getRank1Npe($dateWeek3, $dateWeek2, $brand);
			$topNpes = $em->getRepository('AppBundle:KpiWeek')->getRank1Npes($dateWeek3, $dateWeek2, $brand);
			$topNpesa = $em->getRepository('AppBundle:KpiWeek')->getRank1Npesa($dateWeek3, $dateWeek2, $brand); 

			$topNpeVendeur = $em->getRepository('AppBundle:KpiWeek')->getRank1NpeVendeur($dateWeek3, $dateWeek2, $brand);
			$topNpesVendeur = $em->getRepository('AppBundle:KpiWeek')->getRank1NpesVendeur($dateWeek3, $dateWeek2, $brand);
			$topNpesaVendeur = $em->getRepository('AppBundle:KpiWeek')->getRank1NpesaVendeur($dateWeek3, $dateWeek2, $brand); 
		}

		//Mise à jour du filtre
		$kpiFilterService->updateForm($user, $request, $form);


		$form2 = $this->createForm(new ExportDataType());
        $form2->handleRequest($request);  

		//Export CSV
		if ($form2->isSubmitted()) {
			//$id_data = $user->getId();
			$idDataMarque 	= $marque->getId();
			$idDataFiche 	= $kpiCurrentWeek->getId();
			$idDataAutres	= array();
			if( $user->getRole() == 'ROLE_MARQUE' ) {
				foreach ($getDrsMarque as $key => $DrMarque) {
					array_push($idDataAutres, $DrMarque->getId());
				}
			}
			elseif( $user->getRole() == 'ROLE_DR' ) {
				foreach ($getBoutiquesDr as $key => $BoutiqueDr) {
					array_push($idDataAutres, $BoutiqueDr->getId());
				}
			}
			else {
				foreach ($getVendeursBoutique as $key => $VendeurBoutique) {
					array_push($idDataAutres, $VendeurBoutique->getId());
				}
			}

			$ids = "(".$idDataMarque.",".$idDataFiche;

			foreach ($idDataAutres as $key => $id) {
				$ids .= ",".$id;
			}
			$ids .= ")";

			$sql = "SELECT u.username,u.role,u.brand,u.dr,u.boutique,u.nom_vendeur,u.prenom_vendeur,d.date,d.nb_transac_S0,d.tx_transac_linked_S0,d.tx_transac_npe_S0,d.tx_transac_npes_S0,d.tx_transac_npesa_S0
					FROM app_kpi_week d
					LEFT JOIN fos_user_user u on d.user_id = u.id
					WHERE d.id in $ids";
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
            //var_dump($sql);
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            //Remplissage du fichier csv.
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $row["nb_transac_S0"] = '="'.str_replace('.',',',$row["nb_transac_S0"]).'"';
                $row["tx_transac_linked_S0"] = '="'.str_replace('.',',',$row["tx_transac_linked_S0"]).'"';
                $row["tx_transac_npe_S0"] = '="'.str_replace('.',',',$row["tx_transac_npe_S0"]).'"';
                $row["tx_transac_npes_S0"]  = '="'.str_replace('.',',',$row["tx_transac_npes_S0"]).'"';
                $row["tx_transac_npesa_S0"]  = '="'.str_replace('.',',',$row["tx_transac_npesa_S0"]).'"';
                fputcsv($handle, $row, ';');
            }

            //Fermeture du fichier
            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);
            
            $date_csv = new \Datetime('now');
            $date_csv = $date_csv->format('Ymd');

            //Reponse : Téléchargement du fichier
            return new Response($content, 200, array(
                'Content-Type' => 'application/force-download; text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="export_donnees_'.$date_csv.'.csv"'
            ));

        }

		//Retourne la bonne page
		if($routeName == "app_kpi_week"){
	        return $this->render('AppBundle:Kpi:week.html.twig', array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentWeek,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'topNpeVendeur'		=> $topNpeVendeur,
	        	'topNpesVendeur'	=> $topNpesVendeur,
	        	'topNpesaVendeur'	=> $topNpesaVendeur,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	'form2'          	=> $form2->createView(),
	        	'user_actuel'		=> $user_actuel
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
	public function ajaxfilterAction(User $user_actuel, User $user, $scope, $week, $month, $year, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();

		$form = $this->createForm(new KpiFilterType($em, $user_actuel,$user, $week, $month, $year, $scope));

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
		$kpiFilterService->updateFormAjax($week, $month, $year, $reseau, $dr, $boutique, $form);

	    return $this->render('AppBundle:Ajax:ajaxFilter.html.twig', array(
	    	'user'		=> $user_actuel,
        	'form'     	=> $form->createView(),
        	'scope'		=> $scope
	    	)
	    );
	}
}