<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Client
 *
 * @ORM\Table(name="app_client")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ClientRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Client
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
     * @ORM\Column(name="id_client", type="string", length=255, unique=true)
     */
    private $idClient;

    /**
     * @var string
     *
     * @ORM\Column(name="id_campaign_name", type="string", length=255, nullable=true)
     */
    private $idCampaignName;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=100)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="store", type="string", length=100, nullable=true)
     */
    private $store;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=100, nullable=true)
     */
    private $channel;

    /**
     * @var string
     *
     * @ORM\Column(name="phone1", type="string", length=100, nullable=true)
     */
    private $phone1;

    /**
     * @var string
     *
     * @ORM\Column(name="phone2", type="string", length=70, nullable=true)
     */
    private $phone2;

    /**
     * @var string
     *
     * @ORM\Column(name="adress1", type="string", length=70, nullable=true)
     */
    private $adress1;

    /**
     * @var string
     *
     * @ORM\Column(name="adress2", type="string", length=255, nullable=true)
     */
    private $adress2;

    /**
     * @var string
     *
     * @ORM\Column(name="adress3", type="string", length=255, nullable=true)
     */
    private $adress3;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=10, nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=100, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=100, nullable=true)
     */
    private $nationality;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var integer
     *
     * @ORM\Column(name="ca_3_years", type="integer", nullable=true)
     */
    private $ca3Years;

    /**
     * @var integer
     *
     * @ORM\Column(name="ca_12_months", type="integer", nullable=true)
     */
    private $ca12Months;

    /**
     * @var integer
     *
     * @ORM\Column(name="frequence_3_years", type="integer", nullable=true)
     */
    private $frequence3Years;

    /**
     * @var integer
     *
     * @ORM\Column(name="frequence_12_months", type="integer", nullable=true)
     */
    private $frequence12Months;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_price_3_years", type="integer", nullable=true)
     */
    private $maxPrice3Years;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_price_12_months", type="integer", nullable=true)
     */
    private $maxPrice12Months;

    /**
     * @var string
     *
     * @ORM\Column(name="segment", type="string", length=100, nullable=true)
     */
    private $segment;

    /**
     * @var string
     *
     * @ORM\Column(name="code_uvc", type="string", length=255, nullable=true)
     */
    private $codeUvc;

    /**
     * @var string
     *
     * @ORM\Column(name="sku_desc", type="string", length=255, nullable=true)
     */
    private $skuDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="genre_desc", type="string", length=255, nullable=true)
     */
    private $genreDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne_desc", type="string", length=255, nullable=true)
     */
    private $ligneDesc;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="price_paid", type="integer", nullable=true)
     */
    private $pricePaid;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_1", type="string", length=255, nullable=true)
     */
    private $empty1;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_2", type="string", length=255, nullable=true)
     */
    private $empty2;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_3", type="string", length=255, nullable=true)
     */
    private $empty3;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_4", type="string", length=255, nullable=true)
     */
    private $empty4;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_5", type="string", length=255, nullable=true)
     */
    private $empty5;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_6", type="string", length=255, nullable=true)
     */
    private $empty6;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_7", type="string", length=255, nullable=true)
     */
    private $empty7;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_8", type="string", length=255, nullable=true)
     */
    private $empty8;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_9", type="string", length=255, nullable=true)
     */
    private $empty9;

    /**
     * @var string
     *
     * @ORM\Column(name="empty_10", type="string", length=255, nullable=true)
     */
    private $empty10;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_code", type="string", length=255, nullable=true)
     */
    private $vendorCode;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_contact", type="datetime", nullable=true)
     */
    private $lastContact;

    /**
     * @var boolean
     *
     * @ORM\Column(name="phone_err", type="boolean")
     */
    private $phoneErr;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adress_err", type="boolean")
     */
    private $adressErr;

    /**
     * @var boolean
     *
     * @ORM\Column(name="email_err", type="boolean")
     */
    private $emailErr;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opt_out_email", type="boolean")
     */
    private $optOutEmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opt_out_mail", type="boolean")
     */
    private $optOutMail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opt_out_phone", type="boolean")
     */
    private $optOutPhone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opt_out_sms", type="boolean")
     */
    private $optOutSms;

    /**
     * @var integer
     *
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $modifiedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="campaign_entry_at", type="datetime", nullable=true)
     */
    private $campaignEntryAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Recipient", mappedBy="client", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $recipients;

    public function __construct()
    {
        $this->createdAt    = new \DateTime();
        $this->phoneErr     = 0;
        $this->adressErr    = 0;
        $this->emailErr     = 0;
        $this->optOutEmail  = 0;
        $this->optOutMail   = 0;
        $this->optOutPhone  = 0;
        $this->optOutSms    = 0;
        $this->score        = 0;
        $this->recipients   = new ArrayCollection();
        $this->active       = true;
    }

    /**
     * Get fullName
     *
     * @return \Client
     */
    public function getFullName()
    {
        return $this->firstname.' '.$this->lastname;
    }

    // Function for sonata to render text-link relative to the entity

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getFullName();
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
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set idClient
     *
     * @return Client
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Client
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Client
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
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

    public function getCampaigns()
    {
        return array_map(
            function ($recipient) {
                return $recipient->getCampaign();
            },
            $this->recipients->toArray()
        );
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Client
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set channel
     *
     * @param string $channel
     *
     * @return Client
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set phone1
     *
     * @param string $phone
     *
     * @return Client
     */
    public function setPhone1($phone1)
    {
        $this->phone1 = $phone1;

        return $this;
    }

    /**
     * Get phone1
     *
     * @return string
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * Set phone2
     *
     * @param string $mobile
     *
     * @return Client
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * Get phone2
     *
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set adress1
     *
     * @param string $adress1
     *
     * @return Client
     */
    public function setAdress1($adress1)
    {
        $this->adress1 = $adress1;

        return $this;
    }

    /**
     * Get adress1
     *
     * @return string
     */
    public function getAdress1()
    {
        return $this->adress1;
    }

    /**
     * Set adress2
     *
     * @param string $adress2
     *
     * @return Client
     */
    public function setAdress2($adress2)
    {
        $this->adress2 = $adress2;

        return $this;
    }

    /**
     * Get adress2
     *
     * @return string
     */
    public function getAdress2()
    {
        return $this->adress2;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Client
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Client
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Client
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Client
     */
    public function setBirthday($birthday)
    {
        if( !($birthday instanceof \DateTime) ) $birthday = new \DateTime($birthday);
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set lastContact
     *
     * @param \DateTime $lastContact
     *
     * @return Client
     */
    public function setLastContact($lastContact)
    {
        if( !($lastContact instanceof \DateTime) ) $lastContact = new \DateTime($lastContact);
        $this->lastContact = $lastContact;

        return $this;
    }

    /**
     * Get lastContact
     *
     * @return \DateTime
     */
    public function getLastContact()
    {
        return $this->lastContact;
    }

    /**
     * Set phoneErr
     *
     * @param boolean $phoneErr
     *
     * @return Client
     */
    public function setPhoneErr($phoneErr)
    {
        $this->phoneErr = $phoneErr;

        return $this;
    }

    /**
     * Get phoneErr
     *
     * @return boolean
     */
    public function getPhoneErr()
    {
        return $this->phoneErr;
    }

    /**
     * Set adressErr
     *
     * @param boolean $adressErr
     *
     * @return Client
     */
    public function setAdressErr($adressErr)
    {
        $this->adressErr = $adressErr;

        return $this;
    }

    /**
     * Get adressErr
     *
     * @return boolean
     */
    public function getAdressErr()
    {
        return $this->adressErr;
    }

    /**
     * Set emailErr
     *
     * @param boolean $emailErr
     *
     * @return Client
     */
    public function setEmailErr($emailErr)
    {
        $this->emailErr = $emailErr;

        return $this;
    }

    /**
     * Get emailErr
     *
     * @return boolean
     */
    public function getEmailErr()
    {
        return $this->emailErr;
    }

    /**
     * Set optOutEmail
     *
     * @param boolean $optOutEmail
     *
     * @return Client
     */
    public function setOptOutEmail($optOutEmail)
    {
        $this->optOutEmail = $optOutEmail;

        return $this;
    }

    /**
     * Get optOutEmail
     *
     * @return boolean
     */
    public function getOptOutEmail()
    {
        return $this->optOutEmail;
    }

    /**
     * Set optOutMail
     *
     * @param boolean $optOutMail
     *
     * @return Client
     */
    public function setOptOutMail($optOutMail)
    {
        $this->optOutMail = $optOutMail;

        return $this;
    }

    /**
     * Get optOutMail
     *
     * @return boolean
     */
    public function getOptOutMail()
    {
        return $this->optOutMail;
    }

    /**
     * Set optOutPhone
     *
     * @param boolean $optOutPhone
     *
     * @return Client
     */
    public function setOptOutPhone($optOutPhone)
    {
        $this->optOutPhone = $optOutPhone;

        return $this;
    }

    /**
     * Get optOutPhone
     *
     * @return boolean
     */
    public function getOptOutPhone()
    {
        return $this->optOutPhone;
    }

    /**
     * Set optOutSms
     *
     * @param boolean $optOutSms
     *
     * @return Client
     */
    public function setOptOutSms($optOutSms)
    {
        $this->optOutSms = $optOutSms;

        return $this;
    }

    /**
     * Get optOutSms
     *
     * @return boolean
     */
    public function getOptOutSms()
    {
        return $this->optOutSms;
    }

    /**
     * Set score
     *
     * @param integer $score
     *
     * @return Client
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Client
     */
    public function setCreatedAt($createdAt)
    {
        if( !($createdAt instanceof \DateTime) ) $createdAt = new \DateTime($createdAt);
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     *
     * @return Client
     */
    public function setModifiedAt($modifiedAt)
    {
        if( !($modifiedAt instanceof \DateTime) ) $modifiedAt = new \DateTime($modifiedAt);
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set campaignEntryAt
     *
     * @param \DateTime $campaignEntryAt
     *
     * @return Client
     */
    public function setCampaignEntryAt($campaignEntryAt)
    {
        if( !($campaignEntryAt instanceof \DateTime) ) $campaignEntryAt = new \DateTime($campaignEntryAt);
        $this->campaignEntryAt = $campaignEntryAt;

        return $this;
    }

    /**
     * Get campaignEntryAt
     *
     * @return \DateTime
     */
    public function getCampaignEntryAt()
    {
        return $this->campaignEntryAt;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Campaign
     */
    public function setActive($active)
    {
        $this->active = $active;
        
        return $this;
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set idCampaignName
     *
     * @param string $idCampaignName
     * @return Client
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
     * Set store
     *
     * @param string $store
     * @return Client
     */
    public function setStore($store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Get store
     *
     * @return string 
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set adress3
     *
     * @param string $adress3
     * @return Client
     */
    public function setAdress3($adress3)
    {
        $this->adress3 = $adress3;

        return $this;
    }

    /**
     * Get adress3
     *
     * @return string 
     */
    public function getAdress3()
    {
        return $this->adress3;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     * @return Client
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string 
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set ca3Years
     *
     * @param integer $ca3Years
     * @return Client
     */
    public function setCa3Years($ca3Years)
    {
        $this->ca3Years = $ca3Years;

        return $this;
    }

    /**
     * Get ca3Years
     *
     * @return integer 
     */
    public function getCa3Years()
    {
        return $this->ca3Years;
    }

    /**
     * Set ca12Months
     *
     * @param integer $ca12Months
     * @return Client
     */
    public function setCa12Months($ca12Months)
    {
        $this->ca12Months = $ca12Months;

        return $this;
    }

    /**
     * Get ca12Months
     *
     * @return integer 
     */
    public function getCa12Months()
    {
        return $this->ca12Months;
    }

    /**
     * Set frequence3Years
     *
     * @param integer $frequence3Years
     * @return Client
     */
    public function setFrequence3Years($frequence3Years)
    {
        $this->frequence3Years = $frequence3Years;

        return $this;
    }

    /**
     * Get frequence3Years
     *
     * @return integer 
     */
    public function getFrequence3Years()
    {
        return $this->frequence3Years;
    }

    /**
     * Set frequence12Months
     *
     * @param integer $frequence12Months
     * @return Client
     */
    public function setFrequence12Months($frequence12Months)
    {
        $this->frequence12Months = $frequence12Months;

        return $this;
    }

    /**
     * Get frequence12Months
     *
     * @return integer 
     */
    public function getFrequence12Months()
    {
        return $this->frequence12Months;
    }

    /**
     * Set maxPrice3Years
     *
     * @param integer $maxPrice3Years
     * @return Client
     */
    public function setMaxPrice3Years($maxPrice3Years)
    {
        $this->maxPrice3Years = $maxPrice3Years;

        return $this;
    }

    /**
     * Get maxPrice3Years
     *
     * @return integer 
     */
    public function getMaxPrice3Years()
    {
        return $this->maxPrice3Years;
    }

    /**
     * Set maxPrice12Months
     *
     * @param integer $maxPrice12Months
     * @return Client
     */
    public function setMaxPrice12Months($maxPrice12Months)
    {
        $this->maxPrice12Months = $maxPrice12Months;

        return $this;
    }

    /**
     * Get maxPrice12Months
     *
     * @return integer 
     */
    public function getMaxPrice12Months()
    {
        return $this->maxPrice12Months;
    }

    /**
     * Set segment
     *
     * @param string $segment
     * @return Client
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;

        return $this;
    }

    /**
     * Get segment
     *
     * @return string 
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * Set codeUvc
     *
     * @param string $codeUvc
     * @return Client
     */
    public function setCodeUvc($codeUvc)
    {
        $this->codeUvc = $codeUvc;

        return $this;
    }

    /**
     * Get codeUvc
     *
     * @return string 
     */
    public function getCodeUvc()
    {
        return $this->codeUvc;
    }

    /**
     * Set skuDesc
     *
     * @param string $skuDesc
     * @return Client
     */
    public function setSkuDesc($skuDesc)
    {
        $this->skuDesc = $skuDesc;

        return $this;
    }

    /**
     * Get skuDesc
     *
     * @return string 
     */
    public function getSkuDesc()
    {
        return $this->skuDesc;
    }

    /**
     * Set genreDesc
     *
     * @param string $genreDesc
     * @return Client
     */
    public function setGenreDesc($genreDesc)
    {
        $this->genreDesc = $genreDesc;

        return $this;
    }

    /**
     * Get genreDesc
     *
     * @return string 
     */
    public function getGenreDesc()
    {
        return $this->genreDesc;
    }

    /**
     * Set ligneDesc
     *
     * @param string $ligneDesc
     * @return Client
     */
    public function setLigneDesc($ligneDesc)
    {
        $this->ligneDesc = $ligneDesc;

        return $this;
    }

    /**
     * Get ligneDesc
     *
     * @return string 
     */
    public function getLigneDesc()
    {
        return $this->ligneDesc;
    }

    /**
     * Set pricePaid
     *
     * @param integer $pricePaid
     * @return Client
     */
    public function setPricePaid($pricePaid)
    {
        $this->pricePaid = $pricePaid;

        return $this;
    }

    /**
     * Get pricePaid
     *
     * @return integer 
     */
    public function getPricePaid()
    {
        return $this->pricePaid;
    }

    /**
     * Set empty1
     *
     * @param string $empty1
     * @return Client
     */
    public function setEmpty1($empty1)
    {
        $this->empty1 = $empty1;

        return $this;
    }

    /**
     * Get empty1
     *
     * @return string 
     */
    public function getEmpty1()
    {
        return $this->empty1;
    }

    /**
     * Set empty2
     *
     * @param string $empty2
     * @return Client
     */
    public function setEmpty2($empty2)
    {
        $this->empty2 = $empty2;

        return $this;
    }

    /**
     * Get empty2
     *
     * @return string 
     */
    public function getEmpty2()
    {
        return $this->empty2;
    }

    /**
     * Set empty3
     *
     * @param string $empty3
     * @return Client
     */
    public function setEmpty3($empty3)
    {
        $this->empty3 = $empty3;

        return $this;
    }

    /**
     * Get empty3
     *
     * @return string 
     */
    public function getEmpty3()
    {
        return $this->empty3;
    }

    /**
     * Set empty4
     *
     * @param string $empty4
     * @return Client
     */
    public function setEmpty4($empty4)
    {
        $this->empty4 = $empty4;

        return $this;
    }

    /**
     * Get empty4
     *
     * @return string 
     */
    public function getEmpty4()
    {
        return $this->empty4;
    }

    /**
     * Set empty5
     *
     * @param string $empty5
     * @return Client
     */
    public function setEmpty5($empty5)
    {
        $this->empty5 = $empty5;

        return $this;
    }

    /**
     * Get empty5
     *
     * @return string 
     */
    public function getEmpty5()
    {
        return $this->empty5;
    }

    /**
     * Set empty6
     *
     * @param string $empty6
     * @return Client
     */
    public function setEmpty6($empty6)
    {
        $this->empty6 = $empty6;

        return $this;
    }

    /**
     * Get empty6
     *
     * @return string 
     */
    public function getEmpty6()
    {
        return $this->empty6;
    }

    /**
     * Set empty7
     *
     * @param string $empty7
     * @return Client
     */
    public function setEmpty7($empty7)
    {
        $this->empty7 = $empty7;

        return $this;
    }

    /**
     * Get empty7
     *
     * @return string 
     */
    public function getEmpty7()
    {
        return $this->empty7;
    }

    /**
     * Set empty8
     *
     * @param string $empty8
     * @return Client
     */
    public function setEmpty8($empty8)
    {
        $this->empty8 = $empty8;

        return $this;
    }

    /**
     * Get empty8
     *
     * @return string 
     */
    public function getEmpty8()
    {
        return $this->empty8;
    }

    /**
     * Set empty9
     *
     * @param string $empty9
     * @return Client
     */
    public function setEmpty9($empty9)
    {
        $this->empty9 = $empty9;

        return $this;
    }

    /**
     * Get empty9
     *
     * @return string 
     */
    public function getEmpty9()
    {
        return $this->empty9;
    }

    /**
     * Set empty10
     *
     * @param string $empty10
     * @return Client
     */
    public function setEmpty10($empty10)
    {
        $this->empty10 = $empty10;

        return $this;
    }

    /**
     * Get empty10
     *
     * @return string 
     */
    public function getEmpty10()
    {
        return $this->empty10;
    }

    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     * @return Client
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;

        return $this;
    }

    /**
     * Get vendorCode
     *
     * @return string 
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }
}
