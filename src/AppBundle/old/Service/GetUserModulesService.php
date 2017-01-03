<?php
// src/OC/PlatformBundle/Antispam/OCAntispam.php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

use Application\Sonata\UserBundle\Entity\User;

class GetUserModulesService
{

	public function __construct(EntityManager $entityManager)
	{
		$this->em = $entityManager;
	}

	public function GetUserModules(User $user)
	{
		$userModules = $this->em->getRepository('AppBundle:UserModule')->getUserModules($user);

		$modules = $user->getUserModules();

		//var_dump($modules); die();

		$modules = array();

        foreach ($userModules as $key => $module) {
            if( $module->getModule()->getName() == "Campaign" ) {
                array_push($modules, 'campaign');
            }
            if( $module->getModule()->getName() == "Top Clients" ) {
                array_push($modules, 'topclients');
            }
            if( $module->getModule()->getName() == "Kpi" ) {
                array_push($modules, 'kpi');
            }
        }

        return $modules;
	}
}