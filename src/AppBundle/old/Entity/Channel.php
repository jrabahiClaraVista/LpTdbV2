<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Channel
 *
 * @ORM\Table(name="app_channel")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Channel
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count = 0;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CampaignChannel", mappedBy="channel", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $campaignChannels;

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
     * Set name
     *
     * @param string $name
     * @return Channel
     */
    public function setName($name)
    {
        $this->name = $name;

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

    public function addCampaignChannel(Channel $channel)
    {
        $this->campaignChannels[] = $channel;
        return $this;
    }

    public function removeCampaignChannel(Channel $channel)
    {
        $this->campaignChannels->removeElement($channel);
    }

    public function getCampaignChannels()
    {
        return $this->campaignChannels;
    }

    /**
     * Get count
     *
     * @return integer 
     */
    public function getCount()
    {
        return $this->count;
    }


    public function increaseCount()
    {
        $this->count++;
    }

    public function decreaseCount()
    {
        $this->count--;
    }

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->campaignChannels = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set count
     *
     * @param integer $count
     * @return Channel
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}
