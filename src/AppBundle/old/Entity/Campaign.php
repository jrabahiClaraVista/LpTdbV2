<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Campaign
 *
 * @ORM\Table(name="app_campaign")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CampaignRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Campaign
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="id_campaign_name", type="string", length=255, unique=true, nullable=true)
     */
    private $idCampaignName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     */
    private $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="channel_one", type="string", length=100, nullable=true)
     */
    private $channelOne = null; 

    /**
     * @var string
     *
     * @ORM\Column(name="channel_two", type="string", length=100, nullable=true)
     */
    private $channelTwo = null; 

    /**
     * @var string
     *
     * @ORM\Column(name="channel_three", type="string", length=100, nullable=true)
     */
    private $channelThree = null; 

    /**
     * @var string
     *
     * @ORM\Column(name="channel_four", type="string", length=100, nullable=true)
     */
    private $channelFour = null;  

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=100)
     */
    private $state;

    /**
     * @ORM\Column(name="nb_clients", type="integer")
     */
    private $nbClients = 0;

    /**
     * @ORM\Column(name="nb_contacted", type="integer")
     */
    private $nbContacted = 0;

    /**
     * @ORM\Column(name="nb_opt_out", type="integer")
     */
    private $nbOptOut = 0;

    /**
     * @ORM\Column(name="emails_sent", type="integer")
     */
    private $emailsSent = 0;

    /**
     * @ORM\Column(name="sms_sent", type="integer")
     */
    private $smsSent = 0;

    /**
     * @ORM\Column(name="mails_sent", type="integer")
     */
    private $mailsSent = 0;

    /**
     * @ORM\Column(name="ca", type="integer")
     */
    private $ca = 0;

    /**
     * @ORM\Column(name="tx_o", type="decimal", precision=9, scale=2)
     */
    private $txO = 0;

    /**
     * @ORM\Column(name="tx_c", type="decimal", precision=9, scale=2)
     */
    private $txC = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active_campaign", type="boolean", nullable=true)
     */
    private $activeCampaign;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active_kpi", type="boolean", nullable=true)
     */
    private $activeKpi;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Recipient", mappedBy="campaign", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $recipients;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Image", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $image;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CampaignChannel", mappedBy="campaign", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $campaignChannels;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CampaignField", mappedBy="campaign", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $campaignFields;

    public function __construct()
    {   
        $this->createdAt            = new \DateTime();
        $this->startDate            = new \DateTime();
        $this->endDate              = new \DateTime();
        $this->recipients           = new ArrayCollection();
        $this->campaignChannels     = new ArrayCollection();
        $this->campaignFields       = new ArrayCollection();
        $this->activeCampaign       = false;
        $this->activeKpi            = false;
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idCampaignName
     *
     * @param string $idCampaignName
     *
     * @return Campaign
     */
    public function setIdCampaignName($idCampaignName)
    {
        $this->idCampaignName = $idCampaignName;

        return $this;
    }

    /**
     * Get idCampaignName
     *
     * @return string
     */
    public function getIdCampaignName()
    {
        return $this->idCampaignName;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Campaign
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set brand
     *
     * @param string $brand
     *
     * @return Campaign
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Campaign
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set channelOne
     *
     * @param string $channelOne
     *
     * @return Campaign
     */
    public function setChannelOne($channelOne)
    {
        $this->channelOne = $channelOne;

        return $this;
    }

    /**
     * Get channelOne
     *
     * @return string
     */
    public function getChannelOne()
    {
        return $this->channelOne;
    }

    /**
     * Set channelTwo
     *
     * @param string $channelTwo
     *
     * @return Campaign
     */
    public function SetChannelTwo($channelTwo)
    {
        $this->channelTwo = $channelTwo;

        return $this;
    }

    /**
     * Get channelTwo
     *
     * @return string
     */
    public function getChannelTwo()
    {
        return $this->channelTwo;
    }

    /**
     * Set channelThree
     *
     * @param string $channelThree
     *
     * @return Campaign
     */
    public function setChannelThree($channelThree)
    {
        $this->channelThree = $channelThree;

        return $this;
    }

    /**
     * Get channelThree
     *
     * @return string
     */
    public function getChannelThree()
    {
        return $this->channelThree;
    }

    /**
     * Set channelFour
     *
     * @param string $channelFour
     *
     * @return Campaign
     */
    public function setChannelFour($channelFour)
    {
        $this->channelFour = $channelFour;

        return $this;
    }

    /**
     * Get channelFour
     *
     * @return string
     */
    public function getChannelFour()
    {
        return $this->channelFour;
    }

    /**
     * Set emailsSent
     *
     * @param integer $emailsSent
     *
     * @return Campaign
     */
    public function setEmailsSent($emailsSent)
    {
        $this->emailsSent = $emailsSent;

        return $this;
    }

    /**
     * Get mailsSent
     *
     * @return integer
     */
    public function getMailsSent()
    {
        return $this->mailsSent;
    }

    /**
     * Set mailsSent
     *
     * @param integer $mailsSent
     *
     * @return Campaign
     */
    public function setEMilsSent($mailsSent)
    {
        $this->mailsSent = $mailsSent;

        return $this;
    }

    /**
     * Set smsSent
     *
     * @param integer $smsSent
     *
     * @return Campaign
     */
    public function setSmsSent($smsSent)
    {
        $this->smsSent = $smsSent;

        return $this;
    }

    /**
     * Get smsSent
     *
     * @return integer
     */
    public function getSmsSent()
    {
        return $this->smsSent;
    }

    /**
     * Set ca
     *
     * @param integer $ca
     *
     * @return Campaign
     */
    public function setCa($ca)
    {
        $this->ca = $ca;

        return $this;
    }

    /**
     * Get ca
     *
     * @return integer
     */
    public function getCa()
    {
        return $this->ca;
    }

    /**
     * Set txO
     *
     * @param decimal $txO
     *
     * @return Campaign
     */
    public function setTxO($txO)
    {
        $this->txO = $txO;

        return $this;
    }

    /**
     * Get txO
     *
     * @return decimal
     */
    public function getTxO()
    {
        return $this->txO;
    }

    /**
     * Set txC
     *
     * @param decimal $txC
     *
     * @return Campaign
     */
    public function setTxC($txC)
    {
        $this->txC = $txC;

        return $this;
    }

    /**
     * Get txC
     *
     * @return decimal
     */
    public function getTxC()
    {
        return $this->txC;
    }

    /**
     * Get emailsSent
     *
     * @return integer
     */
    public function getEmailsSent()
    {
        return $this->emailsSent;
    }

    public function addRecipient(Recipient $recipient)
    {
        $this->recipients[] = $recipient;
        return $this;
    }

    public function removeRecipient(Recipient $recipient)
    {
        $this->recipients->removeElement($recipient);
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function addCampaignChannel(CampaignChannel $channel)
    {
        $this->campaignChannels[] = $channel;
        return $this;
    }

    public function removeCampaignChannel(CampaignChannel $channel)
    {
        $this->campaignChannels->removeElement($channel);
    }

    public function getCampaignChannels()
    {
        return $this->campaignChannels;
    }

    public function addCampaignField(Field $field)
    {
        $this->campaignFields[] = $field;
        return $this;
    }

    public function removeCampaignField(Field $field)
    {
        $this->campaignFields->removeElement($field);
    }

    public function getCampaignFields()
    {
        return $this->campaignFields;
    }

    public function setImage(Image $image = null)
    {
        $this->image = $image;
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getClients()
    {
        return array_map(
            function ($recipient) {
                return $recipient->getClient();
            },
            $this->recipients->toArray()
        );
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Campaign
     */
    public function setStartDate($startDate)
    {
        if( !($startDate instanceof \DateTime) ) $startDate = new \DateTime($startDate);
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Campaign
     */
    public function setEndDate($endDate)
    {
        if( !($endDate instanceof \DateTime) ) $endDate = new \DateTime($endDate);
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Campaign
     */
    public function setState()
    {
        $date = new \DateTime();
        $date->setTime(00, 00, 00);

        if ( $date < $this->startDate ) {
            $this->state = "to be launched";
        }
        elseif ( $date <= $this->endDate ) {
            $this->state = "on going";
        }
        else{
            $this->state = "finished";
        }

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set activeCampaign
     *
     * @param boolean $activeCampaign
     *
     * @return Campaign
     */
    public function setActiveCampaign($activeCampaign)
    {
        $this->activeCampaign = $activeCampaign;
        
        return $this;
    }

    /**
     * Get activeCampaign
     *
     * @return string
     */
    public function getActiveCampaign()
    {
        return $this->activeCampaign;
    }

    /**
     * Set activeKpi
     *
     * @param boolean $activeKpi
     *
     * @return Campaign
     */
    public function setActiveKpi($activeKpi)
    {
        $this->activeKpi = $activeKpi;
        
        return $this;
    }

    /**
     * Get activeKpi
     *
     * @return string
     */
    public function getActiveKpi()
    {
        return $this->activeKpi;
    }

    /**
     * Get nbClients
     *
     * @return integer
     */
    public function getNbClients()
    {
        return $this->nbClients;
    }

    /**
     * Set nbClients
     *
     * @param boolean $nbClients
     *
     * @return Campaign
     */
    public function setNbClients($nbClients)
    {
        $this->nbClients = $nbClients;
        
        return $this;
    }

    public function increaseClient()
    {
        $this->nbClients++;
    }

    public function decreaseClient()
    {
        $this->nbClients--;
    }

    /**
     * Get nbContacted
     *
     * @return integer
     */
    public function getNbContacted()
    {
        return $this->nbContacted;
    }

    /**
     * Set nbContacted
     *
     * @param boolean $nbContacted
     *
     * @return Campaign
     */
    public function setNbContacted($nbContacted)
    {
        $this->nbContacted = $nbContacted;
        
        return $this;
    }

    public function increaseContacted()
    {
        $this->nbContacted++;
    }

    public function decreaseContacted()
    {
        $this->nbContacted--;
    }

    /**
     * Get nbOptOut
     *
     * @return integer
     */
    public function getNbOptOut()
    {
        return $this->nbOptOut;
    }

    /**
     * Set nbOptOut
     *
     * @param boolean $nbOptOut
     *
     * @return Campaign
     */
    public function setNbOptOut($nbOptOut)
    {
        $this->nbOptOut = $nbOptOut;
        
        return $this;
    }

    public function increaseOptOut()
    {
        $this->nbOptOut++;
    }

    public function decreaseOptOut()
    {
        $this->nbOptOut--;
    }

    // Function for sonata to render text-link relative to the entity

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateState() {
        $this->setState();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function validationChannels() {
        $channelOne     = $this->getChannelOne();
        $channelTwo     = $this->getChannelTwo();
        $channelThree   = $this->getChannelThree();
        $channelFour    = $this->getChannelFour();

        if( $channelFour == $channelThree || $channelFour == $channelTwo || $channelFour == $channelOne) {
            $this->setChannelFour(null);
        }

        if( $channelThree == $channelTwo || $channelThree == $channelOne) {
            $this->setChannelThree(null);
        }

        if( $channelTwo == $channelOne ) {
            $this->setChannelTwo(null);
        }
    }
}