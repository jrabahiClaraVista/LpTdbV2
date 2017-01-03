<?php

namespace AppBundle\Controller;


// src/OC/PlatformBundle/Controller/AdvertController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Campaign;
use AppBundle\Entity\Client;
use AppBundle\Entity\CampaignClient;
use AppBundle\Entity\Module;
use AppBundle\Entity\UserModule;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Annotaitonss :
// Pour gérer les autorisations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
// Pour gérer le ParamConverter et utiliser un entité en parametre à la place d'une simple variable
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class HomeController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

    	return $this->render('AppBundle:Home:index.html.twig', array(
            
            )
        );
        // replace this example code with whatever you need
        /*return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));*/
    }
    public function testAction(Request $request)
    {
    	$date1 = new \DateTime('2016-04-21');
    	$date2 = new \DateTime();

    	$dateDiff =  $date1->diff($date2);


    	return $this->render('AppBundle:test.html.twig', array(
    		'date1' => $date1,
    		'date2' => $date2,
    		'dateDiff' => $dateDiff->days,
    	));
    }
}
