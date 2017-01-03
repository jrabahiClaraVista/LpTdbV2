<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

//To get access to the EntityManager and UnitOfWork APIs inside these callback methods.
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Recipient
 *
 * @ORM\Table(name="app_recipient")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RecipientRepository")
 * @UniqueEntity(
 *     fields={"campaign", "client"},
 *     message="Duplicate entry for entries Campaign - Client."
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Recipient
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaign", inversedBy="recipients")
     * @ORM\JoinColumn(name="campaign_id", nullable=false)
     */
    private $campaign;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="recipients")
     * @ORM\JoinColumn(name="client_id", nullable=false)
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="opt_out_other", type="string")
     */
    private $optOutOther;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", nullable=true)
     */
    private $channel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opt_out_not_relevant", type="boolean")
     */
    private $optOutNotRelevant;

    /**
     * @var boolean
     *
     * @ORM\Column(name="opt_in", type="boolean")
     */
    private $optIn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="contacted_at", type="datetime", nullable=true)
     */
    private $contactedAt;

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

    public function __construct()
    {
        $this->optOutOther = "";
        $this->optOutNotRelevant = 0;
        $this->optIn = 1;
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

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Client $client = null)
    {
        $this->client = $client;
        return $this;
    }

    public function getCampaign()
    {
        return $this->campaign;
    }

    public function setCampaign(Campaign $campaign = null)
    {
        $this->campaign = $campaign;
        return $this;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Recipient
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return Recipient
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
     * Set optOutOther
     *
     * @param string $optOutOther
     * @return Recipient
     */
    public function setOptOutOther($optOutOther)
    {
        $this->optOutOther = $optOutOther;

        return $this;
    }

    /**
     * Get optOutOther
     *
     * @return string 
     */
    public function getOptOutOther()
    {
        return $this->optOutOther;
    }

    /**
     * Set optOutNotRelevant
     *
     * @param boolean $optOutNotRelevant
     * @return Recipient
     */
    public function setOptOutNotRelevant($optOutNotRelevant)
    {
        $this->optOutNotRelevant = $optOutNotRelevant;

        return $this;
    }

    /**
     * Get optOutNotRelevant
     *
     * @return boolean 
     */
    public function getOptOutNotRelevant()
    {
        return $this->optOutNotRelevant;
    }

    /**
     * Set optIn
     *
     * @param boolean $optIn
     * @return Recipient
     */
    public function setOptIn($optIn)
    {
        $this->optIn = $optIn;

        return $this;
    }

    /**
     * Get optIn
     *
     * @return boolean 
     */
    public function getOptIn()
    {
        return $this->optIn;
    }

    /**
     * Set contactedAt
     *
     * @param \DateTime $contactedAt
     * @return Recipient
     */
    public function setContactedAt($contactedAt)
    {
        $this->contactedAt = $contactedAt;

        return $this;
    }

    /**
     * Get contactedAt
     *
     * @return \DateTime 
     */
    public function getContactedAt()
    {
        return $this->contactedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Recipient
     */
    public function setCreatedAt($createdAt)
    {
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
     * @return Recipient
     */
    public function setModifiedAt($modifiedAt)
    {
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

    public function getCampaignName()
    {
        return $this->campaign->getName();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function increase()
    {
        $this->getCampaign()->increaseClient();
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
        $this->getCampaign()->decreaseClient();
    }

    /**
     * @ORM\PreFlush
     * @ORM\PrePersist
     */
    /*public function updateOptInStatus()
    {
        $client     = $this->client;
        $campaign   = $this->campaign;

        //var_dump($this);die();

        switch ( $campaign->getChannel() )
        {
            case 'email' :
                if ( $client->getOptOutEmail()  == 1 || $this->optOutOther == 1 || $this->optOutNotRelevant == 1 )
                {
                    $this->setOptIn(0);
                }
                else{   
                    $this->setOptIn(1);
                }
            break;
            case 'mail' :
                if ( $client->getOptOutMail()  == 1 || $this->optOutOther == 1 || $this->optOutNotRelevant == 1 )
                {
                    $this->setOptIn(0);
                }
                else{   
                    $this->setOptIn(1);
                }
            break;
            case 'phone' :
                if ( $client->getOptOutPhone()  == 1 || $this->optOutOther == 1 || $this->optOutNotRelevant == 1 )
                {
                    $this->setOptIn(0);
                }
                else{   
                    $this->setOptIn(1);
                }
            break;
            case 'sms' :
                if ( $client->getOptOutSMS()  == 1 || $this->optOutOther == 1 || $this->optOutNotRelevant == 1 )
                {
                    $this->setOptIn(0);
                }
                else{   
                    $this->setOptIn(1);
                }
            break;
        }
        return;
    }  */ 


    // Function for sonata to render text-link relative to the entity

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getCampaignName();
    }


}
