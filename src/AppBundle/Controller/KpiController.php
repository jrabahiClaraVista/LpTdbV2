<?php

namespace AppBundle\Controller;


// src/OC/PlatformBundle/Controller/AdvertController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\Sonata\UserBundle\Entity\User;
use AppBundle\Entity\KpiMonth;
use AppBundle\Entity\KpiYearToDate;

use AppBundle\Form\CampaignKpiType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
	public function monthAction(User $user, $month = null) {
		$em = $this->getDoctrine()->getManager();

		$date = new \DateTime();

		$dateMonth 	= $date->format('m');
		$dateYear 	= $date->format('Y');

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';

		$getBoutiquesDr = null;
		$getDrsMarque = null;

		$kpiCurrentMonth = null;

		//get date range for data month -1 for the year
		switch ($dateMonth) {
			case "01" :
				$date1 = ($dateYear-1)."-01-01";
				$date2 = ($dateYear-1)."-12-31";
			break;
			default :
				$date1 = $dateYear."-01-01";
				$date2 = $dateYear."-12-31";
			break;
		}

		if( $user->getRole() == 'ROLE_DR' ) {
			$getBoutiquesDr = $em->getRepository('ApplicationSonataUserBundle:User')->findBy( array('dr' => $user->getUsername()) );
		}
		if( $user->getRole() == 'ROLE_MARQUE' ) {
			$getDrsMarque = $em->getRepository('ApplicationSonataUserBundle:User')->findBy( array('brand' => $user->getUsername(), 'role' => 'ROLE_DR') );
		}

		$kpis = $em->getRepository('AppBundle:KpiMonth')->getUserKpisBetweenDates($user, $date1, $date2, $brand);

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


		switch ($dateMonth) {
			case "01" :
				$dateMois1 = ($dateYear-1)."-".$month."-01";
				$dateMois2 = ($dateYear-1)."-".$month."-01";
			break;
			default :
				$dateMois1 = $dateYear."-".$month."-01";
				$dateMois2 = $dateYear."-".$month."-01";
			break;
		}

		$topNpe = $em->getRepository('AppBundle:KpiMonth')->getRank1Npe($dateMois1, $dateMois2, $brand);
		$topNpes = $em->getRepository('AppBundle:KpiMonth')->getRank1Npes($dateMois1, $dateMois2, $brand);
		$topNpesa = $em->getRepository('AppBundle:KpiMonth')->getRank1Npesa($dateMois1, $dateMois2, $brand);

		

		if ($kpis == null or $kpiCurrentMonth == null){
			throw new NotFoundHttpException("No data Available");
		}
		
		$currentMonth = $kpis[0]->getDate()->format("m");

        return $this->render('AppBundle:Kpi:month.html.twig', array(
        	'kpis' 				=> $kpis,
        	'kpiCurrentMonth' 	=> $kpiCurrentMonth,
        	'topNpe'			=> $topNpe,
        	'topNpes'			=> $topNpes,
        	'topNpesa'			=> $topNpesa,
        	'month'				=> $month,
        	'currentMonth'		=> $currentMonth,
        	'user'				=> $user,
        	'getBoutiquesDr'	=> $getBoutiquesDr,
        	'getDrsMarque'		=> $getDrsMarque
        	)
        );
	}

	/**
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
	public function ytdAction(User $user, $year = null) {
		$em = $this->getDoctrine()->getManager();

		$date = new \DateTime();

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';
		
		if ($year == null) {
			if ($date->format('m') == 01)
				$year = $date->format('Y') - 1;
			else 
				$year = $date->format('Y');
		}

		$kpis = $em->getRepository('AppBundle:KpiYearToDate')->getUserKpiYtd($user->getUsername(), $year, $brand);

		$topNpe = $em->getRepository('AppBundle:KpiYearToDate')->getRank1Npe($year, $brand);
		$topNpes = $em->getRepository('AppBundle:KpiYearToDate')->getRank1Npes($year, $brand);
		$topNpesa = $em->getRepository('AppBundle:KpiYearToDate')->getRank1Npesa($year, $brand);

		if ($kpis == null){
			throw new NotFoundHttpException("No data Available");			
		}

        return $this->render('AppBundle:Kpi:ytd.html.twig', array(
        	'kpiCurrentYear'	=> $kpis,
        	'year'				=> $year,
        	'topNpe'			=> $topNpe,
        	'topNpes'			=> $topNpes,
        	'topNpesa'			=> $topNpesa,
        	'user'				=> $user,
        	'month'				=> $kpis->getDate()->format('m')

        	)
        );
	}

	/**
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
	public function fidAction(User $user) {
		$em = $this->getDoctrine()->getManager();

		$kpis = $em->getRepository('AppBundle:KpiMonth')->findOneByUser($user, array(
				'date' => 'DESC'
			)
		);
		$date = $kpis->getDate();

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';

		$kpisYtd = $em->getRepository('AppBundle:KpiYearToDate')->findOneBy(array(
				'user' => $user,
				'date' => $date
			)
		);

		//orderBy('k.caClientsTransformesM0', 'DESC')
		$kpisTopCa = $em->getRepository('AppBundle:KpiMonth')->getTop3Ca($brand, $date);
		$tauxCumul = array();

		foreach ($kpisTopCa as $key => $top) {
			$cumul = $em->getRepository('AppBundle:KpiYearToDate')->findOneBy(array(
				'username' => $top->getUsername(),
				'date' => $date
				), array(
					'date' => 'DESC'
				)
			);

			$tauxCumul[$key] = $cumul->getTxTransacNpeYtd();
		}

        return $this->render('AppBundle:Kpi:fid.html.twig', array(
        	'kpis' 			=> $kpis,
        	'kpisYtd'		=> $kpisYtd,
        	'kpisTopCa'		=> $kpisTopCa,
        	'topTauxCumul'	=> $tauxCumul,
        	'user'			=> $user,
        	'month'			=> $kpis->getDate()->format('m')
        	)
        );
	}

	/**
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
	public function planningAction(User $user, $month = null) {
		$em = $this->getDoctrine()->getManager();

		$date = new \DateTime();

		$dateMonth 	= $date->format('m');
		$dateYear 	= $date->format('Y');

		if($dateMonth == "01" ){
			$currentMonth = "12";
			$currentYear = $date->format('Y')-1;
		}else{
			$currentMonth = $date->format('m') - 1;
			$currentYear = $date->format('Y');
		}		

		$brand = $user->getBrand();
		if ($brand == null) $brand = '';

		//get date range for data month -1 for the year // Get campaign by month
		if($month == null)
		{
			$month = $currentMonth;
			switch ($dateMonth) {
				case "01" :
					$date1 = ($dateYear-1)."-12-01";
					$date2 = $dateYear."-01-01";
				break;
				default :
					$date1 = $dateYear."-01-01";
					$date2 = $dateYear."-12-01";
				break;
			}
		}else{
			switch ($month) {
				default :
					$date1 = $currentYear."-".($month)."-01";
					$date2 = $currentYear."-".($month+1)."-01";
				break;
			}
		}

		$campaigns = $em->getRepository('AppBundle:Campaign')->getCampaignsOfMonth($date1, $date2, $user->getBrand());

        return $this->render('AppBundle:Kpi:planning.html.twig', array(
        	'campaigns' 		=> $campaigns,
        	'month'				=> $month,
        	'currentMonth'		=> $currentMonth,
        	'user'				=> $user,
        	'month'				=> $month
        	)
        );
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
}