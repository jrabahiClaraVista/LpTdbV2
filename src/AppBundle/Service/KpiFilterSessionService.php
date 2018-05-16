<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KpiFilterSessionService
{
    private $container;
    private $session;
    private $em;

    public function __construct(ContainerInterface $container, EntityManager $entityManager)
    {

        $this->container = $container;
        $this->em = $entityManager;
        $this->session = $this->container->get('session');

    }

    public function initVars($user, $request)
    {
        //Init var
        $reseau                             = "";
        $dr                                 = "";
        $boutique                           = "";
        $vendeur                            = "";

        //Set up options Filtre dynamique en focntion du role de l'utilisateur connecté.

        $vars = array(  $reseau,
                        $dr,
                        $boutique,
                        $vendeur
                );

        return $vars;
    }

    public function updateSessionVars($options){

        $reseau     = $options["reseau"];
        $dr         = $options["dr"];
        $boutique   = $options["boutique"];
        $vendeur    = $options["vendeur"];

        if($reseau == '' ){
            $this->session->remove('filtre_reseau');
        }
        else{
            $this->session->set('filtre_reseau', $reseau);
        }
        if($dr == '' ){
            $this->session->remove('filtre_dr');
        }
        else{
            $this->session->set('filtre_dr', $dr);
        }

        if($boutique == '' ){
            $this->session->remove('filtre_boutique');
        }
        else{
            $this->session->set('filtre_boutique', $boutique);
        }

        if($vendeur == '' ){
            $this->session->remove('filtre_vendeur');
        }
        else{
            $this->session->set('filtre_vendeur', $vendeur);
        }

        return;
    }

    public function updateForm($user, $request, $form){
        $userEm =  $this->em->getRepository('ApplicationSonataUserBundle:User');

        if($this->session->get('filtre_reseau') != null && $request->getMethod() == 'GET'){
            $form->get('reseau')->setData($userEm->findOneBy(array('username' => $this->session->get('filtre_reseau')->getUsername())));
        }
        else{
            if($user->getRole() == 'ROLE_MARQUE'){
                $form->get('reseau')->setData($user);
            }
        }
        if($this->session->get('filtre_dr') != null && $request->getMethod() == 'GET'){
            $dr     = $userEm->findOneBy(array('username' => $this->session->get('filtre_dr')->getUsername()));
            $reseau = $userEm->findOneBy(array('username' => $dr->getBrand()));

            $form->get('dr')->setData($dr);
            $form->get('reseau')->setData($reseau);
        }
        else{
            if($user->getRole() == 'ROLE_DR'){
                $dr     = $userEm->findOneBy(array('username' => $user->getUsername()));
                $reseau = $userEm->findOneBy(array('username' => $dr->getBrand()));

                $form->get('dr')->setData($dr);
                $form->get('reseau')->setData($reseau);
            }
        }
        if($this->session->get('filtre_boutique') != null && $request->getMethod() == 'GET'){
            $boutique   = $userEm->findOneBy(array('username' => $this->session->get('filtre_boutique')->getUsername()));
            $dr         = $userEm->findOneBy(array('username' => $boutique->getDr()));
            $reseau     = $userEm->findOneBy(array('username' => $boutique->getBrand()));

            $form->get('boutique')->setData($boutique);
            $form->get('dr')->setData($dr);
            $form->get('reseau')->setData($reseau);
        }
        else{
            if($user->getRole() == 'ROLE_BOUTIQUE'){
                $boutique   = $userEm->findOneBy(array('username' => $user->getUsername()));
                $dr         = $userEm->findOneBy(array('username' => $boutique->getDr()));
                $reseau     = $userEm->findOneBy(array('username' => $boutique->getBrand()));

                $form->get('boutique')->setData($boutique);
                $form->get('dr')->setData($dr);
                $form->get('reseau')->setData($reseau);
            }
        }
        if($this->session->get('filtre_vendeur') != null && $request->getMethod() == 'GET'){
            $vendeur    = $userEm->findOneBy(array('username' => $this->session->get('filtre_vendeur')->getUsername()));
            $boutique   = $userEm->findOneBy(array('username' => $this->session->get('filtre_boutique')->getUsername()));
            $dr         = $userEm->findOneBy(array('username' => $boutique->getDr()));
            $reseau     = $userEm->findOneBy(array('username' => $boutique->getBrand()));

            $form->get('boutique')->setData($boutique);
            $form->get('vendeur')->setData($vendeur);
            $form->get('dr')->setData($dr);
            $form->get('reseau')->setData($reseau);
        }
        else{
            if($user->getRole() == 'ROLE_VENDEUR'){
                $vendeur    = $userEm->findOneBy(array('username' => $user->getUsername()));
                $boutique   = $userEm->findOneBy(array('username' => $user->getBoutique()));
                $dr         = $userEm->findOneBy(array('username' => $boutique->getDr()));
                $reseau     = $userEm->findOneBy(array('username' => $boutique->getBrand()));

                $form->get('boutique')->setData($boutique);
                $form->get('vendeur')->setData($vendeur);
                $form->get('dr')->setData($dr);
                $form->get('reseau')->setData($reseau);
            }
        }

        return $form;
    }

    public function updateFormAjax($week, $month, $year, $reseau, $dr, $boutique, $form){
        $form->get('reseau')->setData($reseau);
        $form->get('dr')->setData($dr);
        $form->get('boutique')->setData($boutique);
        $form->get('week')->setData($week);
        $form->get('month')->setData($month);
        $form->get('year')->setData($year);

        return $form;
    }
}
