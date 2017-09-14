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
 * KpiMonth
 *
 * @ORM\Table(name="app_kpi_week", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="UNIQUE_USER_DATE", columns={"user_id", "date"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Entity\KpiWeekRepository")
 * @UniqueEntity(fields={"user_id", "date"})
 * @ORM\HasLifecycleCallbacks()
 */
class KpiWeek
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
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="kpiWeeks")
     */
    private $user;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;


    /*
     *
     *  KPI MONTH
     *
     */
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nb_transac_S0", type="integer")
     */
    private $nbTransacS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_linked_S0", type="decimal", precision=9, scale=2)
     */
    private $txtransaclinkedS0;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npe_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeS0;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nve_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNveS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npes_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nves_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesa_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvesa_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesaS0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npe_S0", type="integer")
     */
    private $rankNpeS0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npes_S0", type="integer")
     */
    private $rankNpesS0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npesa_S0", type="integer")
     */
    private $rankNpesaS0 = 1;


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
     * Set nbTransacS0
     *
     * @param integer $nbTransacS0
     * @return KpiMonth
     */
    public function setNbTransacS0($nbTransacS0)
    {
        $this->nbTransacS0 = $nbTransacS0;

        return $this;
    }

    /**
     * Get nbTransacS0
     *
     * @return integer 
     */
    public function getNbTransacS0()
    {
        return $this->nbTransacS0;
    }

    /**
     * Set txTransacNpeS0
     *
     * @param decimal $txTransacNpeS0
     * @return KpiMonth
     */
    public function setTxTransacNpeS0($txTransacNpeS0)
    {
        $this->txTransacNpeS0 = $txTransacNpeS0;

        return $this;
    }

    /**
     * Get txTransacNpeS0
     *
     * @return decimal 
     */
    public function getTxTransacNpeS0()
    {
        return $this->txTransacNpeS0;
    }

    /**
     * Set txtransaclinkedS0
     *
     * @param decimal $txtransaclinkedS0
     * @return KpiMonth
     */
    public function setTxTransacLinkedS0($txtransaclinkedS0)
    {
        $this->txtransaclinkedS0 = $txtransaclinkedS0;

        return $this;
    }

    /**
     * Get txtransaclinkedS0
     *
     * @return decimal 
     */
    public function getTxTransacLinkedS0()
    {
        return $this->txtransaclinkedS0;
    }

    /**
     * Set txTransacNveS0
     *
     * @param decimal $txTransacNveS0
     * @return KpiMonth
     */
    public function setTxTransacNveS0($txTransacNveS0)
    {
        $this->txTransacNveS0 = $txTransacNveS0;

        return $this;
    }

    /**
     * Get txTransacNveS0
     *
     * @return decimal 
     */
    public function getTxTransacNveS0()
    {
        return $this->txTransacNveS0;
    }

    /**
     * Set txTransacNpesS0
     *
     * @param decimal $txTransacNpesS0
     * @return KpiMonth
     */
    public function setTxTransacNpesS0($txTransacNpesS0)
    {
        $this->txTransacNpesS0 = $txTransacNpesS0;

        return $this;
    }

    /**
     * Get txTransacNpesS0
     *
     * @return decimal 
     */
    public function getTxTransacNpesS0()
    {
        return $this->txTransacNpesS0;
    }

    /**
     * Set txTransacNvesS0
     *
     * @param decimal $txTransacNvesS0
     * @return KpiMonth
     */
    public function setTxTransacNvesS0($txTransacNvesS0)
    {
        $this->txTransacNvesS0 = $txTransacNvesS0;

        return $this;
    }

    /**
     * Get txTransacNvesS0
     *
     * @return decimal 
     */
    public function getTxTransacNvesS0()
    {
        return $this->txTransacNvesS0;
    }

    /**
     * Set txTransacNpesaS0
     *
     * @param decimal $txTransacNpesaS0
     * @return KpiMonth
     */
    public function setTxTransacNpesaS0($txTransacNpesaS0)
    {
        $this->txTransacNpesaS0 = $txTransacNpesaS0;

        return $this;
    }

    /**
     * Get txTransacNpesaS0
     *
     * @return decimal 
     */
    public function getTxTransacNpesaS0()
    {
        return $this->txTransacNpesaS0;
    }

    /**
     * Set txTransacNvesaS0
     *
     * @param decimal $txTransacNvesaS0
     * @return KpiMonth
     */
    public function setTxTransacNvesaS0($txTransacNvesaS0)
    {
        $this->txTransacNvesaS0 = $txTransacNvesaS0;

        return $this;
    }

    /**
     * Get txTransacNvesaS0
     *
     * @return decimal 
     */
    public function getTxTransacNvesaS0()
    {
        return $this->txTransacNvesaS0;
    }

    /**
     * Set rankNpeS0
     *
     * @param string $rankNpeS0
     * @return KpiMonth
     */
    public function setRankNpeS0($rankNpeS0)
    {
        $this->rankNpeS0 = $rankNpeS0;

        return $this;
    }

    /**
     * Get rankNpeS0
     *
     * @return string 
     */
    public function getRankNpeS0()
    {
        return $this->rankNpeS0;
    }

    /**
     * Set rankNpesS0
     *
     * @param string $rankNpesS0
     * @return KpiMonth
     */
    public function setRankNpesS0($rankNpesS0)
    {
        $this->rankNpesS0 = $rankNpesS0;

        return $this;
    }

    /**
     * Get rankNpesS0
     *
     * @return string 
     */
    public function getRankNpesS0()
    {
        return $this->rankNpesS0;
    }

    /**
     * Set rankNpesaS0
     *
     * @param string $rankNpesaS0
     * @return KpiMonth
     */
    public function setRankNpesaS0($rankNpesaS0)
    {
        $this->rankNpesaS0 = $rankNpesaS0;

        return $this;
    }

    /**
     * Get rankNpesaS0
     *
     * @return string 
     */
    public function getRankNpesaS0()
    {
        return $this->rankNpesaS0;
    }

    // Function for sonata to render text-link relative to the entity

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getId();
    }
}
