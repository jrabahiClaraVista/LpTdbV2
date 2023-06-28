<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Application\Sonata\UserBundle\Entity\User;

/**
 * KpiTrimestreHebdo
 *
 * @ORM\Table(name="app_kpi_trim_hebdo", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="UNIQUE_USER_DATE", columns={"user_id", "date"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Entity\KpiTrimHebdoRepository")
 * @UniqueEntity(fields={"user_id", "date"})
 * @ORM\HasLifecycleCallbacks()
 */
class KpiTrimHebdo
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
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="kpiTrims")
     */
    private $user;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_calcul", type="date", nullable=true)
     */
    private $date_calcul;

    /*
     *
     *  KPI TRIM
     *
     */
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nb_desabo", type="integer")
     */
    private $nbDesaboT0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nb_hardbounce", type="integer")
     */
    private $nbHardbounceT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="objectif_desabo", type="integer")
     */
    private $objectifDesaboT0;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="objectif_hardbounce", type="integer")
     */
    private $objectifHardbounceT0;
    
    

    /*
     *
     *  GETTERS / SETTERS
     *
     */


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return kpiMonth
     */
    public function setDate($date)
    {
        if( !($date instanceof \DateTime) ) $date = new \DateTime($date);
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date_calcul
     *
     * @param \DateTime $date_calcul
     *
     * @return kpiMonth
     */
    public function setDateCalcul($date_calcul)
    {
        if( !($date_calcul instanceof \DateTime) ) $date_calcul = new \DateTime($date_calcul);
        $this->date_calcul = $date_calcul;

        return $this;
    }

    /**
     * Get date_calcul
     *
     * @return \DateTime
     */
    public function getDateCalcul()
    {
        return $this->date_calcul;
    }

    /**
     * Set nbDesaboT0
     *
     * @param integer $nbDesaboT0
     * @return KpiMonth
     */
    public function setNbDesaboT0($nbDesaboT0)
    {
        $this->nbDesaboT0 = $nbDesaboT0;

        return $this;
    }

    /**
     * Get nbDesaboT0
     *
     * @return integer 
     */
    public function getNbDesaboT0()
    {
        return $this->nbDesaboT0;
    }

    /**
     * Set nbHardbounceT0
     *
     * @param integer $nbHardbounceT0
     * @return KpiMonth
     */
    public function setNbHardbounceT0($nbHardbounceT0)
    {
        $this->nbHardbounceT0 = $nbHardbounceT0;

        return $this;
    }

    /**
     * Get nbHardbounceT0
     *
     * @return integer 
     */
    public function getNbHardbounceT0()
    {
        return $this->nbHardbounceT0;
    }

    /**
     * Set objectifDesaboT0
     *
     * @param integer $objectifDesaboT0
     * @return KpiMonth
     */
    public function setObjectifDesaboT0($objectifDesaboT0)
    {
        $this->objectifDesaboT0 = $objectifDesaboT0;

        return $this;
    }

    /**
     * Get objectifDesaboT0
     *
     * @return integer 
     */
    public function getObjectifDesaboT0()
    {
        return $this->objectifDesaboT0;
    }

    /**
     * Set objectifHardbounceT0
     *
     * @param integer $objectifHardbounceT0
     * @return KpiMonth
     */
    public function setObjectifHardbounceT0($objectifHardbounceT0)
    {
        $this->objectifHardbounceT0 = $objectifHardbounceT0;

        return $this;
    }

    /**
     * Get objectifHardbounceT0
     *
     * @return integer 
     */
    public function getObjectifHardbounceT0()
    {
        return $this->objectifHardbounceT0;
    }
}