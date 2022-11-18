<?php

namespace AppBundle\Service;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class initKpiFilterDatesService
{
	/*private $container;

	public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }*/
    
    /**
     * @param $date     date de référence
     * @param $session  $_SESSION
     * @param $trigger  null ou 1 si utilisé pour récupérer les kpis des triggers
     *
     */
    public function getDates(\Datetime $date, $session)
    {   
        $now = new \DateTime();
        $nowY = $now->format('Y');

        $diff = $nowY - $date->format('Y');

        //la derniere date est toujours celle du dernier kpicapture en base, la premiere varie de -12 à -24 mois
        //Affichage des données du dernier mois / mois selectionné : du premier à la fin du mois.
        //On test aussi les var de session month et year, car par defaut pour TOT la valeur est a null
        if( $session->get('kpi_month_filtre') == null || $session->get('kpi_year_filtre') == null ) {
            $month = $date->format('m');
            $year  = $date->format('Y');
            $week  = $date->format('W');

            $date2 = new \DateTime($date->format('Y-m-d'));
            $date2->modify('last day of this month');
            $date2 = $date2->format("Y-m-d");
        }
        //si on a une recherche active
        else{
            $month  = $session->get('kpi_month_filtre');
            $year  = $session->get('kpi_year_filtre');

            $date2 = new \DateTime($year."-".$month."-01");
            $date2->modify('last day of this month');
            $date2 = $date2->format("Y-m-d");

            $date_check = new \DateTime($date2);

            if($date_check > $date) {
                $month = $date->format('m');
                $year  = $date->format('Y');

                $session->set('kpi_month_filtre', $month);
                $session->set('kpi_year_filtre', $year);

                $date2 = new \DateTime($date->format('Y-m-d'));
                $date2->modify('last day of this month');
                $date2 = $date2->format("Y-m-d");
            }
        }

        $date1 = new \DateTime($date2);
        $date3 = new \DateTime($date2);

        // Si on on veut récupérer les infos trigger on a une plage de données sur 1 mois
        $date3->modify('first day of this month');
        // Si on veut récupérer les données sur les 12 derniers mois
        $date1->modify('-12 months')->modify('first day of this month');

        $date1 = $date1->format("Y-m-d");
        $date3 = $date3->format("Y-m-d");

        $results['date1'] = $date1;
        $results['date2'] = $date2;
        $results['date3'] = $date3;
        $results['month'] = $month;
        $results['year']  = $year;

        return $results;
    }

    /**
     * @param $date     date de référence
     * @param $session  $_SESSION
     * @param $trigger  null ou 1 si utilisé pour récupérer les kpis des triggers
     *
     */
    public function getDatesTrim(\Datetime $date, $session)
    { 
        $now = new \DateTime();

        //mois en cours par defaut 
        $month = date('n')-3; 
        $trim = floor(($month-1)/3)+1;
        $year = $now->format('Y');

        //la derniere date est toujours celle du dernier kpicapture en base, la premiere varie de -12 à -24 mois
        //Affichage des données du dernier mois / mois selectionné : du premier à la fin du mois.
        //On test aussi les var de session month et year, car par defaut pour TOT la valeur est a null
        if( $session->get('kpi_trim_filtre') == null  ) {

            //on initialise la date2 au dernier jour de la semaine
            //$dateWeek2 = $now->setISODate($year,$week,7);//->modify('-1 month');
            $dateTrim2 = $now->modify('-3 months')->modify('first day of this month');
        }
        //si on a une recherche active
        else{
            $trim  = $session->get('kpi_trim_filtre');
            $year  = $session->get('kpi_year_filtre');

            //$dateTrim2 = $now->setISODate($year,$week,7);//->modify('-1 month');
            //$dateTrim2 = $now->setISODate($year,$week,7);
            $dateTrim2 = new \DateTime($year."-".$month."-01");
            $dateTrim2 = $dateTrim2->format("Y-m-d");

            $date_check = new \DateTime($dateTrim2);

            if($date_check > $date) {
                $week  = $date->format('W');
                $year  = $date->format('Y');

                $session->set('kpi_trim_filtre', $trim);
                $session->set('kpi_year_filtre', $year);

                if($trim == 1) {
                    $month = "01";
                }
                elseif($trim == 2) {
                    $month = "04";
                }
                elseif($trim == 3) {
                    $month = "07";
                }
                elseif($trim == 4) {
                    $month = "10";
                }

                $dateTrim2 = new \DateTime($year."-".$month."-01");
                $dateTrim2 = $dateTrim2->format("Y-m-d");
            }
        }

        $dateTrim1 = new \DateTime($dateTrim2);
        $dateTrim3 = new \DateTime($dateTrim2);

        // Si on on veut récupérer les infos sur une plage de données d'1 trimestre
        $dateTrim3->modify('first day of this month');
        // Si on veut récupérer les données sur les 8 derniers trimestre
        $dateTrim1->modify('-24 months')->modify('first day of this month');

        $dateTrim1 = $dateTrim1->format("Y-m-d");
        $dateTrim3 = $dateTrim3->format("Y-m-d");



        $results['dateTrim1']   = $dateTrim1;
        $results['dateTrim2']   = $dateTrim2;
        $results['dateTrim3']   = $dateTrim3;
        $results['trim']        = $trim;
        $results['year']        = $year;

        return $results;
    }

    /**
     * @param $date     date de référence
     * @param $session  $_SESSION
     * @param $trigger  null ou 1 si utilisé pour récupérer les kpis des triggers
     *
     */
    public function getDatesWeek(\Datetime $date, $session)
    { 
        $now = new \DateTime();
        $check = new \DateTime();
        if ($check->format('w') == '1'){
            $now = $now->modify('last monday');
        }
        else {
            $now = $now->modify('last monday')->modify('-1 week');//->modify('-1 month');    
        }
        
        $week  = $date->format('W');
        $year = $now->format('Y');

        //la derniere date est toujours celle du dernier kpicapture en base, la premiere varie de -12 à -24 mois
        //Affichage des données du dernier mois / mois selectionné : du premier à la fin du mois.
        //On test aussi les var de session month et year, car par defaut pour TOT la valeur est a null
        if( $session->get('kpi_week_filtre') == null  ) {

            //on initialise la date2 au dernier jour de la semaine
            //$dateWeek2 = $now->setISODate($year,$week,7);//->modify('-1 month');
            $dateWeek2 = $now;
            $dateWeek2 = $dateWeek2->format("Y-m-d");
        }
        //si on a une recherche active
        else{
            $week  = $session->get('kpi_week_filtre');
            $year  = $session->get('kpi_year_filtre');

            //$dateWeek2 = $now->setISODate($year,$week,7);//->modify('-1 month');
            $dateWeek2 = $now->setISODate($year,$week,7);
            $dateWeek2 = $dateWeek2->format("Y-m-d");

            $date_check = new \DateTime($dateWeek2);

            if($date_check > $date) {
                $week  = $date->format('W');
                $year  = $date->format('Y');

                $session->set('kpi_week_filtre', $week);
                $session->set('kpi_year_filtre', $year);

                $dateWeek2 = $now->setISODate($year,$week,7);//->modify('-1 month');
                $dateWeek2 = $dateWeek2->format("Y-m-d");
            }
        }

        $dateWeek1 = new \DateTime($dateWeek2);
        $dateWeek3 = new \DateTime($dateWeek2);

        // Si on on veut récupérer les infos sur une plage de données d'1 semaine
        $dateWeek3->modify('-6 days');
        // Si on veut récupérer les données sur les 6 derniers mois
        $dateWeek1->modify('-6 days')->modify('-168 days');

        $dateWeek1 = $dateWeek1->format("Y-m-d");
        $dateWeek3 = $dateWeek3->format("Y-m-d");



        $results['dateWeek1']   = $dateWeek1;
        $results['dateWeek2']   = $dateWeek2;
        $results['dateWeek3']   = $dateWeek3;
        $results['week']        = $week;
        $results['year']        = $year;

        return $results;
    }

    /**
     * @param $date     date de référence
     * @param $session  $_SESSION
     * @param $trigger  null ou 1 si utilisé pour récupérer les kpis des triggers
     *
     */
    public function getDatesPost($data, $session, $trigger = null){
        //Set Session variable
        if($data['month'] == '' ){
            $session->remove('kpi_month_filtre');
        }
        else{
            $session->set('kpi_month_filtre', $data['month']);
        }
        if($data['year'] == '' ){
            $session->remove('kpi_year_filtre');
        }
        else{
            $session->set('kpi_year_filtre', $data['year']);
        }

        // On récupère les bons mois et année FY
        $month = $session->get('kpi_month_filtre');
        $year  = $session->get('kpi_year_filtre');
        $week  = $session->get('kpi_week_filtre');


        $date2 = new \DateTime($year."-".$month."-01");
        $date2->modify('last day of this month');
        $date2 = $date2->format("Y-m-d");
        
        $date1 = new \DateTime($date2);
        $date3 = new \DateTime($date2);
        
        $date3->modify('first day of this month');
        $date1->modify('-12 months')->modify('first day of this month');
        
        $date1 = $date1->format("Y-m-d");
        $date3 = $date3->format("Y-m-d");

        $results['date1'] = $date1;
        $results['date2'] = $date2;
        $results['date3'] = $date3;
        $results['month'] = $month;
        $results['year']  = $year;

        return $results;
    }

    /**
     * @param $date     date de référence
     * @param $session  $_SESSION
     * @param $trigger  null ou 1 si utilisé pour récupérer les kpis des triggers
     *
     */
    public function getDatesWeekPost($data, $session, $trigger = null){
        $now = new \DateTime();
        $now = $now->modify('-7 days');
        $year = $now->format('Y');
        $week = $now->format('W');

        if(intval($week) < intval($data['week'])) {
            $data['year'] =  strval(intval($year) - 1);
        }
        else{
            $data['year'] = $year;
        }

        //Set Session variable
        if($data['year'] == '' ){
            $session->remove('kpi_year_filtre');
        }
        else{
            $session->set('kpi_year_filtre',$data['year']);
        }
        if( isset($data['week']) ){
            if($data['week'] == '' ){
                $session->remove('kpi_week_filtre');
            }
            else{
                $session->set('kpi_week_filtre', $data['week']);
            }
        }


        // On récupère les bons mois et année FY
        $year  = $session->get('kpi_year_filtre');
        $week  = $session->get('kpi_week_filtre');
       

        $dateWeek2 = new \DateTime();
        $dateWeek2 = $dateWeek2->setISODate($year,$week,7);
        $dateWeek2 = $dateWeek2->format("Y-m-d");
        
        $dateWeek1 = new \DateTime($dateWeek2);
        $dateWeek3 = new \DateTime($dateWeek2);
        
        $dateWeek3->modify('-6 days');
        $dateWeek1->modify('-6 days')->modify('-168 days');
        
        $dateWeek1 = $dateWeek1->format("Y-m-d");
        $dateWeek3 = $dateWeek3->format("Y-m-d");

        $results['dateWeek1']   = $dateWeek1;
        $results['dateWeek2']   = $dateWeek2;
        $results['dateWeek3']   = $dateWeek3;
        $results['year']        = $year;
        $results['week']        = $week;
        
        return $results;
    }



    /**
     * @param $date     date de référence
     * @param $session  $_SESSION
     * @param $trigger  null ou 1 si utilisé pour récupérer les kpis des triggers
     *
     */
    public function getDatesTrimPost($data, $session, $trigger = null){
        $now = new \DateTime();
        $year = $now->format('Y');
        $month = $now->format('n');

        if(intval($trim) < intval($data['trim'])) {
            $data['year'] =  strval(intval($year) - 1);
        }
        else{
            $data['year'] = $year;
        }

        //Set Session variable
        if($data['year'] == '' ){
            $session->remove('kpi_year_filtre');
        }
        else{
            $session->set('kpi_year_filtre',$data['year']);
        }
        if( isset($data['trim']) ){
            if($data['trim'] == '' ){
                $session->remove('kpi_week_filtre');
            }
            else{
                $session->set('kpi_week_filtre', $data['trim']);
            }
        }


        // On récupère les bons mois et année FY
        $year  = $session->get('kpi_year_filtre');
        $trim  = $session->get('kpi_week_filtre');
       

        $dateTrim2 = new \DateTime();
        $dateTrim2 = $dateTrim2->setISODate($year,$trim,7);
        $dateTrim2 = $dateTrim2->format("Y-m-d");
        
        $dateTrim1 = new \DateTime($dateTrim2);
        $dateTrim3 = new \DateTime($dateTrim2);
        
        // Si on on veut récupérer les infos sur une plage de données d'1 trimestre
        $dateTrim3->modify('first day of this month');
        // Si on veut récupérer les données sur les 8 derniers trimestre
        $dateTrim1->modify('-24 months')->modify('first day of this month');
        
        $dateTrim1 = $dateTrim1->format("Y-m-d");
        $dateTrim3 = $dateTrim3->format("Y-m-d");

        $results['dateTrim1']   = $dateTrim1;
        $results['dateTrim2']   = $dateTrim2;
        $results['dateTrim3']   = $dateTrim3;
        $results['year']        = $year;
        $results['trim']        = $trim;
        
        return $results;
    }
}