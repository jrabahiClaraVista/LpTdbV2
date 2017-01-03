<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Field
 *
 * @ORM\Table(name="app_field")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Field
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count = 0;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CampaignField", mappedBy="field", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    private $campaignFields;

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
     * @return Field
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

    public function addCampaignFields(Field $field)
    {
        $this->campaignFields[] = $field;
        return $this;
    }

    public function removeCampaignfield(Field $field)
    {
        $this->campaignFields->removeElement($field);
    }

    public function getCampaignFields()
    {
        return $this->campaignFields;
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
        $this->campaignFields = new \Doctrine\Common\Collections\ArrayCollection();
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
