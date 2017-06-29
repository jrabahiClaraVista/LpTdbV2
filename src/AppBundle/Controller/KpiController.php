<?php

namespace AppBundle\Controller;


// src/OC/PlatformBundle/Controller/AdvertController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\Sonata\UserBundle\Entity\User;
use AppBundle\Entity\KpiWeek;
use AppBundle\Entity\KpiMonth;

use AppBundle\Form\CampaignKpiType;
use AppBundle\Form\KpiFilterType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
	public function kpiAction(User $user, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();
		$routeName = $request->get('_route');

		$session->remove('filtre_reseau');
		$session->remove('filtre_dr');
		$session->remove('filtre_boutique');
		$session->remove('filtre_vendeur');

		$lastKpi = $em->getRepository('AppBundle:KpiMonth')->findOneBy(array('user' => $user), array('date' => "DESC"));

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

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$marque = $em->getRepository('AppBundle:KpiMonth')->getKpiMarque($date3, $date2, $user->getUsername());
		}
		else{
			$marque = $em->getRepository('AppBundle:KpiMonth')->getKpiMarque($date3, $date2, $user->getBrand());	
		}

        if ( $request->getMethod() == 'POST' ) {
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

			if($session->get('filtre_boutique') != null){
				//var_dump( $data);
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

			if($routeName == "app_kpi_month"){
				return $this->redirectToRoute('app_kpi_month', array('user_id' =>$id));
			}
			if($routeName == "app_kpi_ytd"){
				return $this->redirectToRoute('app_kpi_ytd', array('user_id' =>$id));
			}
			if($routeName == "app_kpi_fid"){
				return $this->redirectToRoute('app_kpi_fid', array('user_id' =>$id));
			}
			if($routeName == "app_kpi_planning"){
				return $this->redirectToRoute('app_kpi_planning', array('user_id' =>$id));
			}
        }

        //Gestion des requêtes selon la page appelée
        
        if($session->get('filtre_boutique') != null){
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
				if ( $kpi->getDate()->format("m") == $month ) {
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
				return $this->redirectToRoute('app_kpi_month', array('user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_ytd"){
				return $this->redirectToRoute('app_kpi_ytd', array('user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_fid"){
				return $this->redirectToRoute('app_kpi_fid', array('user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_planning"){
				return $this->redirectToRoute('app_kpi_planning', array('user_id' =>$user->getId()));
			}
		}

		//Récupération des top
		if($routeName == "app_kpi_month"){
			$topNpe = $em->getRepository('AppBundle:KpiMonth')->getRank1Npe($date3, $date2, $brand);
			$topNpes = $em->getRepository('AppBundle:KpiMonth')->getRank1Npes($date3, $date2, $brand);
			$topNpesa = $em->getRepository('AppBundle:KpiMonth')->getRank1Npesa($date3, $date2, $brand); 
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

		//Retourne la bonne page
		if($routeName == "app_kpi_month"){
	        return $this->render('AppBundle:Kpi:month.html.twig', array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentMonth,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'currentMonth'		=> $currentMonth,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
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
	        	'marque'		=> $marque,
		        'getBoutiquesDr'	=> $getBoutiquesDr,
		        'getDrsMarque'		=> $getDrsMarque,
		        'getVendeursBoutique' => $getVendeursBoutique,
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
	        	'user'				=> $user
	        	)
	        );
		}
	}


	/**
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
	public function kpiWeekAction(User $user, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$session = $request->getSession();
		$routeName = $request->get('_route');

		$session->remove('filtre_reseau');
		$session->remove('filtre_dr');
		$session->remove('filtre_boutique');
		$session->remove('filtre_vendeur');

		$lastKpiWeek = $em->getRepository('AppBundle:KpiWeek')->findOneBy(array('user' => $user), array('date' => "DESC"));

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

		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$marque = $em->getRepository('AppBundle:KpiWeek')->getKpiMarque($dateWeek3, $dateWeek2, $user->getUsername());
		}
		else{
			$marque = $em->getRepository('AppBundle:KpiWeek')->getKpiMarque($dateWeek3, $dateWeek2, $user->getBrand());	
		}

        if ( $request->getMethod() == 'POST' ) {
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

			if($session->get('filtre_boutique') != null){
				//var_dump( $data);
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
				return $this->redirectToRoute('app_kpi_week', array('user_id' =>$id));
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
		
		//get current month depending on url parameter
		foreach ($kpis as $key => $kpi) {
			
			if ( $week == null ) {
				if ( $key == 0 )
					$kpiCurrentWeek = $kpi;
					$month = $kpiCurrentWeek->getDate()->format("W");
			}
			else {
				if ( $kpi->getDate()->format("W") == $week ) {
					$kpiCurrentWeek = $kpi;
				}
			}
		}

		if ($kpis == null or $kpiCurrentWeek == null){
			//throw new NotFoundHttpException("No data Available");
			//$kpiCurrentWeek = null;
			$session->remove('kpi_month_filtre');
            $session->remove('kpi_year_filtre');
            $session->remove('kpi_week_filtre');

            if($routeName == "app_kpi_month"){
				return $this->redirectToRoute('app_kpi_month', array('user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_ytd"){
				return $this->redirectToRoute('app_kpi_ytd', array('user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_fid"){
				return $this->redirectToRoute('app_kpi_fid', array('user_id' =>$user->getId()));
			}
			if($routeName == "app_kpi_planning"){
				return $this->redirectToRoute('app_kpi_planning', array('user_id' =>$user->getId()));
			}
		}

		//Récupération des top
		if($routeName == "app_kpi_week"){
			$topNpe = $em->getRepository('AppBundle:KpiWeek')->getRank1Npe($dateWeek3, $dateWeek2, $brand);
			$topNpes = $em->getRepository('AppBundle:KpiWeek')->getRank1Npes($dateWeek3, $dateWeek2, $brand);
			$topNpesa = $em->getRepository('AppBundle:KpiWeek')->getRank1Npesa($dateWeek3, $dateWeek2, $brand); 
		}

		//Mise à jour du filtre
		$kpiFilterService->updateForm($user, $request, $form);


		//Retourne la bonne page
		if($routeName == "app_kpi_week"){
	        return $this->render('AppBundle:Kpi:week.html.twig', array(
	        	'kpis' 				=> $kpis,
	        	'currentKpi'	 	=> $kpiCurrentWeek,
	        	'topNpe'			=> $topNpe,
	        	'topNpes'			=> $topNpes,
	        	'topNpesa'			=> $topNpesa,
	        	'user'				=> $user,
	        	'getBoutiquesDr'	=> $getBoutiquesDr,
	        	'getDrsMarque'		=> $getDrsMarque,
	        	'getVendeursBoutique' => $getVendeursBoutique,
	        	'marque'			=> $marque,
	        	'form'          	=> $form->createView(),
	        	)
	        );
		}
	}

	/**
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
	public function faqAction(User $user) {

		return $this->render('AppBundle:Kpi:faq.html.twig', array(
        	'user'				=> $user,
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
        	'form'     	=> $form->createView()
	    	)
	    );
	}
}