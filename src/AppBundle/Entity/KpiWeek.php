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


    /**
     * Nouvelle variables Avril 2019
     */

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_linked_optin_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacLinkedOptinS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_linked_optout_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacLinkedOptoutS0;


    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npei_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeiS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npeo_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeoS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesi_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesiS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npeso_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesoS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesai_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaiS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesao_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaoS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nps_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpsS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npsi_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpsiS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npso_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpsoS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvs_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvsS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npa_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpaS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npai_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpaiS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npao_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpaoS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nva_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvaS0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npes2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpes2S0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesi2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesi2S0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npeso2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeso2S0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nves2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNves2S0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesa2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesa2S0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesai2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesai2S0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesao2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesao2S0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvesa2_S0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesa2S0;

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



    public function getTxTransacNpeiS0()
    {
        return $this->txTransacNpeiS0;
    }
     
    public function setTxTransacNpeiS0($txTransacNpeiS0)
    {
        $this->txTransacNpeiS0 = $txTransacNpeiS0;
        return $this;
    }
    public function getTxTransacNpeoS0()
    {
        return $this->txTransacNpeoS0;
    }
     
    public function setTxTransacNpeoS0($txTransacNpeoS0)
    {
        $this->txTransacNpeoS0 = $txTransacNpeoS0;
        return $this;
    }
    public function getTxTransacNpesiS0()
    {
        return $this->txTransacNpesiS0;
    }
     
    public function setTxTransacNpesiS0($txTransacNpesiS0)
    {
        $this->txTransacNpesiS0 = $txTransacNpesiS0;
        return $this;
    }
    public function getTxTransacNpesoS0()
    {
        return $this->txTransacNpesoS0;
    }
     
    public function setTxTransacNpesoS0($txTransacNpesoS0)
    {
        $this->txTransacNpesoS0 = $txTransacNpesoS0;
        return $this;
    }
    public function getTxTransacNpesaiS0()
    {
        return $this->txTransacNpesaiS0;
    }
     
    public function setTxTransacNpesaiS0($txTransacNpesaiS0)
    {
        $this->txTransacNpesaiS0 = $txTransacNpesaiS0;
        return $this;
    }
    public function getTxTransacNpesaoS0()
    {
        return $this->txTransacNpesaoS0;
    }
     
    public function setTxTransacNpesaoS0($txTransacNpesaoS0)
    {
        $this->txTransacNpesaoS0 = $txTransacNpesaoS0;
        return $this;
    }
    public function getTxTransacNpsS0()
    {
        return $this->txTransacNpsS0;
    }
     
    public function setTxTransacNpsS0($txTransacNpsS0)
    {
        $this->txTransacNpsS0 = $txTransacNpsS0;
        return $this;
    }
    public function getTxTransacNpsiS0()
    {
        return $this->txTransacNpsiS0;
    }
     
    public function setTxTransacNpsiS0($txTransacNpsiS0)
    {
        $this->txTransacNpsiS0 = $txTransacNpsiS0;
        return $this;
    }
    public function getTxTransacNpsoS0()
    {
        return $this->txTransacNpsoS0;
    }
     
    public function setTxTransacNpsoS0($txTransacNpsoS0)
    {
        $this->txTransacNpsoS0 = $txTransacNpsoS0;
        return $this;
    }
    public function getTxTransacNvsS0()
    {
        return $this->txTransacNvsS0;
    }
     
    public function setTxTransacNvsS0($txTransacNvsS0)
    {
        $this->txTransacNvsS0 = $txTransacNvsS0;
        return $this;
    }
    public function getTxTransacNpaS0()
    {
        return $this->txTransacNpaS0;
    }
     
    public function setTxTransacNpaS0($txTransacNpaS0)
    {
        $this->txTransacNpaS0 = $txTransacNpaS0;
        return $this;
    }
    public function getTxTransacNpaiS0()
    {
        return $this->txTransacNpaiS0;
    }
     
    public function setTxTransacNpaiS0($txTransacNpaiS0)
    {
        $this->txTransacNpaiS0 = $txTransacNpaiS0;
        return $this;
    }
    public function getTxTransacNpaoS0()
    {
        return $this->txTransacNpaoS0;
    }
     
    public function setTxTransacNpaoS0($txTransacNpaoS0)
    {
        $this->txTransacNpaoS0 = $txTransacNpaoS0;
        return $this;
    }
    public function getTxTransacNvaS0()
    {
        return $this->txTransacNvaS0;
    }
     
    public function setTxTransacNvaS0($txTransacNvaS0)
    {
        $this->txTransacNvaS0 = $txTransacNvaS0;
        return $this;
    }
    public function getTxTransacNpes2S0()
    {
        return $this->txTransacNpes2S0;
    }
     
    public function setTxTransacNpes2S0($txTransacNpes2S0)
    {
        $this->txTransacNpes2S0 = $txTransacNpes2S0;
        return $this;
    }
    public function getTxTransacNpesi2S0()
    {
        return $this->txTransacNpesi2S0;
    }
     
    public function setTxTransacNpesi2S0($txTransacNpesi2S0)
    {
        $this->txTransacNpesi2S0 = $txTransacNpesi2S0;
        return $this;
    }
    public function getTxTransacNpeso2S0()
    {
        return $this->txTransacNpeso2S0;
    }
     
    public function setTxTransacNpeso2S0($txTransacNpeso2S0)
    {
        $this->txTransacNpeso2S0 = $txTransacNpeso2S0;
        return $this;
    }
    public function getTxTransacNves2S0()
    {
        return $this->txTransacNves2S0;
    }
     
    public function setTxTransacNves2S0($txTransacNves2S0)
    {
        $this->txTransacNves2S0 = $txTransacNves2S0;
        return $this;
    }
    public function getTxTransacNpesa2S0()
    {
        return $this->txTransacNpesa2S0;
    }
     
    public function setTxTransacNpesa2S0($txTransacNpesa2S0)
    {
        $this->txTransacNpesa2S0 = $txTransacNpesa2S0;
        return $this;
    }
    public function getTxTransacNpesai2S0()
    {
        return $this->txTransacNpesai2S0;
    }
     
    public function setTxTransacNpesai2S0($txTransacNpesai2S0)
    {
        $this->txTransacNpesai2S0 = $txTransacNpesai2S0;
        return $this;
    }
    public function getTxTransacNpesao2S0()
    {
        return $this->txTransacNpesao2S0;
    }
     
    public function setTxTransacNpesao2S0($txTransacNpesao2S0)
    {
        $this->txTransacNpesao2S0 = $txTransacNpesao2S0;
        return $this;
    }
    public function getTxTransacNvesa2S0()
    {
        return $this->txTransacNvesa2S0;
    }
     
    public function setTxTransacNvesa2S0($txTransacNvesa2S0)
    {
        $this->txTransacNvesa2S0 = $txTransacNvesa2S0;
        return $this;
    }


    public function getTxTransacLinkedOptinS0()
    {
        return $this->txTransacLinkedOptinS0;
    }
     
    public function setTxTransacLinkedOptinS0($txTransacLinkedOptinS0)
    {
        $this->txTransacLinkedOptinS0 = $txTransacLinkedOptinS0;
        return $this;
    }


    public function getTxTransacLinkedOptoutS0()
    {
        return $this->txTransacLinkedOptoutS0;
    }
     
    public function setTxTransacLinkedOptoutS0($txTransacLinkedOptoutS0)
    {
        $this->txTransacLinkedOptoutS0 = $txTransacLinkedOptoutS0;
        return $this;
    }

}
