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
 * CampaignChannel
 *
 * @ORM\Table(name="app_campaign_channel")
 * @ORM\Entity
 * @UniqueEntity(fields={"campaign", "channel"}, message="Duplicate entry for entities Campaign and Channel")
 * @ORM\HasLifecycleCallbacks()
 */
class CampaignChannel
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaign", inversedBy="campaignChannels")
     * @ORM\JoinColumn(name="campaign_id", nullable=false)
     */
    private $campaign;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Channel", inversedBy="campaignChannels")
     * @ORM\JoinColumn(name="channel_id", nullable=false)
     */
    private $channel;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel = null)
    {
        $this->channel = $channel;
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
     * @ORM\PrePersist
     */
    public function increaseCount()
    {
        $this->getChannel()->increaseCount();
    }

    /**
     * @ORM\PreRemove
     */
    public function decreaseCount()
    {
        $this->getChannel()->decreaseCount();
    }
     


    // Function for sonata to render text-link relative to the entity

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getChannel()->getName();
    }


}
