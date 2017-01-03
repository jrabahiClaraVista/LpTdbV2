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
 * CampaignField
 *
 * @ORM\Table(name="app_campaign_field")
 * @ORM\Entity()
 * @UniqueEntity(fields={"campaign", "field"}, message="Duplicate entry for entities Campaign and Field")
 * @ORM\HasLifecycleCallbacks()
 */
class CampaignField
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaign", inversedBy="campaignFields")
     * @ORM\JoinColumn(name="campaign_id", nullable=false)
     */
    private $campaign;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Field", inversedBy="campaignFields")
     * @ORM\JoinColumn(name="field_id", nullable=false)
     */
    private $field;


    public function __construct()
    {

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

    public function getField()
    {
        return $this->field;
    }

    public function setField(Field $field = null)
    {
        $this->field = $field;
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
        $this->getField()->increaseCount();
    }

    /**
     * @ORM\PreRemove
     */
    public function decreaseCount()
    {
        $this->getField()->decreaseCount();
    }
     


    // Function for sonata to render text-link relative to the entity

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getField()->getName();
    }


}
