<?php

namespace AppBundle\Controller;


// src/OC/PlatformBundle/Controller/AdvertController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Campaign;
use AppBundle\Entity\Client;
use AppBundle\Entity\Recipient;

use AppBundle\Form\ExcludeClientFromCampaignType;
use AppBundle\Form\RecipientType;
use AppBundle\Form\RecipientCommentType;
use AppBundle\Form\RecipientValidateContactType;
use AppBundle\Form\ChangeChannelRecipientType;
use AppBundle\Form\ClientType;
use AppBundle\Form\CampaignType;
use AppBundle\Form\SendEmailType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

// Annotaitonss :
// Pour gérer les autorisations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
// Pour gérer le ParamConverter et utiliser un entité en parametre à la place d'une simple variable
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CampaignController extends Controller
{
    public function indexAction(Request $request)
    {
        $userModules = $this->container->get('app.get_user_modules');
        $modules = $userModules->GetUserModules( $this->get('security.context')->getToken()->getUser()  );
        
        if( !in_array("campaign", $modules) ){
            throw new AccessDeniedHttpException();
        }

    	$em = $this->getDoctrine()->getManager();

        //$campaigns = $em->getRepository('AppBundle:Campaign')->getCampaigns();
        $campaigns = $em->getRepository('AppBundle:Campaign')->getActiveCampaigns();
        //$campaigns = $em->getRepository('AppBundle:Campaign')->getActiveCampaignsList();
        
        foreach ($campaigns as $key => $campaign) {
            $campaign->setNbOptOut($em->getRepository('AppBundle:Recipient')->getNbOptOut($campaign));
            $campaign->setNbClients($em->getRepository('AppBundle:Recipient')->getTotalRecipientsForCampaign($campaign));
            $campaign->setNbContacted($em->getRepository('AppBundle:Recipient')->getNbContacted($campaign));
        }

        // replace this example code with whatever you need
        return $this->render('AppBundle:Campaign:index.html.twig', array(
        	'campaigns' => $campaigns
        	)
        );
    }

    /**
     * @ParamConverter("campaign", options={"mapping": {"campaign_id": "id"}})
     */
    public function viewCampaignClientsListAction(Campaign $campaign)
    {
        $em = $this->getDoctrine()->getManager();

        $recipients = $em->getRepository('AppBundle:Recipient')->getClientsForCampaign($campaign);

        // replace this example code with whatever you need
        return $this->render('AppBundle:Campaign:viewCampaignClientsList.html.twig', array(
            'campaign'  => $campaign,
            'recipients'   => $recipients
            )
        );
    }

    /**
     * @ParamConverter("campaign", options={"mapping": {"campaign_id": "id"}})
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function viewCampaignClientAction(Campaign $campaign, Client $client, Request $request)
    {   
        $em = $this->getDoctrine()->getManager();

        $recipient = $em->getRepository('AppBundle:Recipient')->findOneBy(array('campaign' => $campaign, 'client' => $client));

        $form   =  $this->createForm(new ExcludeClientFromCampaignType(), $recipient);
        $formTwo  =  $this->createForm(new RecipientCommentType(), $recipient);
        $formThree  =  $this->createForm(new ChangeChannelRecipientType(), $recipient);
        $formFour  =  $this->createForm(new RecipientValidateContactType(), $recipient);

        $form->handleRequest($request);
        $formTwo->handleRequest($request);
        $formThree->handleRequest($request);
        $formFour->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            //persist inutile, Doctrine connait l'entité
            
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Client updated.');

            return $this->redirect($this->generateUrl('app_campaign_clients_list', array('campaign_id' => $recipient->getCampaign()->getId())));
        }
        if ( $formTwo->isSubmitted() && $formTwo->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            //persist inutile, Doctrine connait l'entité
            
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Comment added for client.');

            return $this->redirect($this->generateUrl('app_campaign_clients_list', array('campaign_id' => $recipient->getCampaign()->getId())));
        }
        if ( $formThree->isSubmitted() && $formThree->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            
            //persist inutile, Doctrine connait l'entité
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Recipient Channel changed to: ' . $recipient->getChannel() . '.');            
        }
        if ( $formFour->isSubmitted() && $formFour->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $recipient->setContactedAt(new \DateTime());
            $client->setLastContact(new \DateTime());

            //persist inutile, Doctrine connait l'entité
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Contact validated.');

            return $this->redirect($this->generateUrl('app_campaign_clients_list', array('campaign_id' => $recipient->getCampaign()->getId())));
        }
        
        // replace this example code with whatever you need
        return $this->render('AppBundle:Campaign:viewCampaignClient.html.twig', array(
            'campaign'  => $campaign,
            'client'    => $client,
            'recipient' => $recipient,
            'form'      => $form->createView(),
            'formTwo'   => $formTwo->createView(),
            'formThree' => $formThree->createView(),
            'formFour'  => $formFour->createView(),
            )
        );
    }

    /**
     * @ParamConverter("recipient", options={"mapping": {"recipient_id": "id"}})
     */
    public function CampaignSendEmailAction(Recipient $recipient, Request $request)
    {   
        $user= $this->get('security.context')->getToken()->getUser();

        $client = $recipient->getClient();
        $campaign = $recipient->getCampaign();        

        $form   =  $this->createForm(new SendEmailType());

        $form->handleRequest($request);

        if ( $form->isSubmitted() ) {
            $em = $this->getDoctrine()->getManager();

            $data = $form->getData();

            if( isset($data['bcc']) ) {
                //Get multiple Bcc and Cs as array
                if($data['bcc'] != ""){
                    $bcc = array_map('trim', explode(',', $data['bcc'])); 
                }
                else $bcc = null;
            }
            else $bcc = null;
            if( isset($data['cc']) ) {
                if($data['cc'] != ""){
                    $cc = array_map('trim', explode(',', $data['cc'])); 
                }
                else $cc = null;

            }
            else $cc = null;

            //Get user webMail
            if ( $user->getWebMail() == 'gmail' ) {
                $webMail = 'smtp.gmail.com';
            }
            else {
                $webMail = 'smtp-mail.outlook.com';
            }

            //Set up Transport with user Webmail and password
            $transport = \Swift_SmtpTransport::newInstance( $webMail, 587, 'tls' )
                        ->setUsername($user->getEmail())
                        ->setPassword($data['password']);

            //Create mailer with new Transport  credentials
            $mailer = \Swift_Mailer::newInstance($transport);
            
            //Setup message
            $message = \Swift_Message::newInstance()
                ->setSubject($data['subject'])
                ->setFrom($user->getEmail())
                ->setTo($recipient->getClient()->getEmail())
                ->setCc($cc)
                ->setBody(
                    $this->renderView(
                        // app/Resources/views/Emails/registration.html.twig
                        'AppBundle:Emails:default.html.twig',
                        array('message' => $data['message'])
                    ),
                    'text/html'
                )
            ;

            //Try to send message and get exeption if fail
            try{
                $mailer->send($message);
                $success = 1;
            }
            catch(\Swift_TransportException $e){
                $success = 0;
                $response = $e->getMessage() ;
                $request->getSession()->getFlashBag()->add('notice', 'Error while sending the email, check your credentials.');
                //var_dump($response); die();
            }

            if ($bcc){
                foreach ($bcc as $key => $copy) {
                    $message->setSubject("[Copy] - ".$data['subject'])
                            ->setTo($copy);
                    $mailer->send($message);
                }
            }

            //if message send is success update bdd
            if ($success) {
                $em = $this->getDoctrine()->getManager();
                $recipient->setContactedAt(new \DateTime());
                $client->setLastContact(new \DateTime());

                //persist inutile, Doctrine connait l'entité
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Email sent successfully.');
                return $this->redirect($this->generateUrl('app_campaign_clients_list', array('campaign_id' => $recipient->getCampaign()->getId())));
            }
        }

        return $this->render('AppBundle:Campaign:viewCampaignSendEmail.html.twig', array(
            'recipient'   => $recipient,
            'campaign'    => $campaign,
            'client'      => $client,
            'form'        => $form->createView(),
            )
        );
    }
}
