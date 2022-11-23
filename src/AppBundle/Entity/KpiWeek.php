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
 * KpiWeek
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
     *  KPI WEEK
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


    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npe2_s0", type="integer")
     */
    private $rankNpe2S0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_nps2_s0", type="integer")
     */
    private $rankNps2S0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npes2_s0", type="integer")
     */
    private $rankNpes2S0 = 1;

    /*
     *
     *  KPI SATISFACTION
     *
     */

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_questsatisf_s0", type="integer")
     */
    private $nbrequestsatisfs0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_questsatisf_montred_s0", type="integer")
     */
    private $nbrequestsatisfmontreds0;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_questsatisf_piled_s0", type="integer")
     */
    private $nbrequestsatisfpileds0;

    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_quest_satisf_promoteur_s0", type="decimal", precision=9, scale=2)
     */
    private $txquestsatisfpromoteurds0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_quest_satisf_passif_s0", type="decimal", precision=9, scale=2)
     */
    private $txquestsatisfpassifs0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_quest_satisf_detracteur_s0", type="decimal", precision=9, scale=2)
     */
    private $txquestsatisfdetracteurs0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="quest_satisf_nps_s0", type="decimal", precision=9, scale=2)
     */
    private $questsatisfnpss0;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="quest_satisf_rank_nps_s0", type="integer")
     */
    private $questsatisfranknpss0;


    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q2_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq2s0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q3_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq3s0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q4_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq4s0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q5_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq5s0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q6_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq6s0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_pile_q2_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfpileq2s0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_pile_q3_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfpileq3s0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_pile_q4_s0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfpileq4s0;


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


    /**
     * Set rankNpe2S0
     *
     * @param string $rankNpe2S0
     * @return KpiMonth
     */
    public function setRankNpe2S0($rankNpe2S0)
    {
        $this->rankNpe2S0 = $rankNpe2S0;

        return $this;
    }

    /**
     * Get rankNpe2S0
     *
     * @return string 
     */
    public function getRankNpe2S0()
    {
        return $this->rankNpe2S0;
    }

    /**
     * Set rankNps2S0
     *
     * @param string $rankNps2S0
     * @return KpiMonth
     */
    public function setRankNps2S0($rankNps2S0)
    {
        $this->rankNps2S0 = $rankNps2S0;

        return $this;
    }

    /**
     * Get rankNps2S0
     *
     * @return string 
     */
    public function getRankNps2S0()
    {
        return $this->rankNps2S0;
    }

    /**
     * Set rankNpes2S0
     *
     * @param string $rankNpes2S0
     * @return KpiMonth
     */
    public function setRankNpes2S0($rankNpes2S0)
    {
        $this->rankNpes2S0 = $rankNpes2S0;

        return $this;
    }

    /**
     * Get rankNpes2S0
     *
     * @return string 
     */
    public function getRankNpes2S0()
    {
        return $this->rankNpes2S0;
    }

    /**
     * Set nbrequestsatisfs0
     *
     * @param integer $nbrequestsatisfs0
     * @return KpiWeek
     */
    public function setNbrequestsatisfs0($nbrequestsatisfs0)
    {
        $this->nbrequestsatisfs0 = $nbrequestsatisfs0;

        return $this;
    }

    /**
     * Get nbrequestsatisfs0
     *
     * @return integer 
     */
    public function getNbrequestsatisfs0()
    {
        return $this->nbrequestsatisfs0;
    }

    /**
     * Set nbrequestsatisfmontreds0
     *
     * @param integer $nbrequestsatisfmontreds0
     * @return KpiWeek
     */
    public function setNbrequestsatisfmontreds0($nbrequestsatisfmontreds0)
    {
        $this->nbrequestsatisfmontreds0 = $nbrequestsatisfmontreds0;

        return $this;
    }

    /**
     * Get nbrequestsatisfmontreds0
     *
     * @return integer 
     */
    public function getNbrequestsatisfmontreds0()
    {
        return $this->nbrequestsatisfmontreds0;
    }

    /**
     * Set nbrequestsatisfpileds0
     *
     * @param integer $nbrequestsatisfpileds0
     * @return KpiWeek
     */
    public function setNbrequestsatisfpileds0($nbrequestsatisfpileds0)
    {
        $this->nbrequestsatisfpileds0 = $nbrequestsatisfpileds0;

        return $this;
    }

    /**
     * Get nbrequestsatisfpileds0
     *
     * @return integer 
     */
    public function getNbrequestsatisfpileds0()
    {
        return $this->nbrequestsatisfpileds0;
    }

    /**
     * Set txquestsatisfpromoteurds0
     *
     * @param integer $txquestsatisfpromoteurds0
     * @return KpiWeek
     */
    public function setTxquestsatisfpromoteurds0($txquestsatisfpromoteurds0)
    {
        $this->txquestsatisfpromoteurds0 = $txquestsatisfpromoteurds0;

        return $this;
    }

    /**
     * Get txquestsatisfpromoteurds0
     *
     * @return integer 
     */
    public function getTxquestsatisfpromoteurds0()
    {
        return $this->txquestsatisfpromoteurds0;
    }

    /**
     * Set txquestsatisfpassifs0
     *
     * @param string $txquestsatisfpassifs0
     * @return KpiWeek
     */
    public function setTxquestsatisfpassifs0($txquestsatisfpassifs0)
    {
        $this->txquestsatisfpassifs0 = $txquestsatisfpassifs0;

        return $this;
    }

    /**
     * Get txquestsatisfpassifs0
     *
     * @return string 
     */
    public function getTxquestsatisfpassifs0()
    {
        return $this->txquestsatisfpassifs0;
    }

    /**
     * Set txquestsatisfdetracteurs0
     *
     * @param string $txquestsatisfdetracteurs0
     * @return KpiWeek
     */
    public function setTxquestsatisfdetracteurs0($txquestsatisfdetracteurs0)
    {
        $this->txquestsatisfdetracteurs0 = $txquestsatisfdetracteurs0;

        return $this;
    }

    /**
     * Get txquestsatisfdetracteurs0
     *
     * @return string 
     */
    public function getTxquestsatisfdetracteurs0()
    {
        return $this->txquestsatisfdetracteurs0;
    }

    /**
     * Set questsatisfnpss0
     *
     * @param string $questsatisfnpss0
     * @return KpiWeek
     */
    public function setQuestsatisfnpss0($questsatisfnpss0)
    {
        $this->questsatisfnpss0 = $questsatisfnpss0;

        return $this;
    }

    /**
     * Get questsatisfnpss0
     *
     * @return string 
     */
    public function getQuestsatisfnpss0()
    {
        return $this->questsatisfnpss0;
    }

    /**
     * Set questsatisfranknpss0
     *
     * @param integer $questsatisfranknpss0
     * @return KpiWeek
     */
    public function setQuestsatisfranknpss0($questsatisfranknpss0)
    {
        $this->questsatisfranknpss0 = $questsatisfranknpss0;

        return $this;
    }

    /**
     * Get questsatisfranknpss0
     *
     * @return integer 
     */
    public function getQuestsatisfranknpss0()
    {
        return $this->questsatisfranknpss0;
    }

    /**
     * Set moyquestsatisfmontreq2s0
     *
     * @param string $moyquestsatisfmontreq2s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfmontreq2s0($moyquestsatisfmontreq2s0)
    {
        $this->moyquestsatisfmontreq2s0 = $moyquestsatisfmontreq2s0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq2s0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq2s0()
    {
        return $this->moyquestsatisfmontreq2s0;
    }

    /**
     * Set moyquestsatisfmontreq3s0
     *
     * @param string $moyquestsatisfmontreq3s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfmontreq3s0($moyquestsatisfmontreq3s0)
    {
        $this->moyquestsatisfmontreq3s0 = $moyquestsatisfmontreq3s0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq3s0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq3s0()
    {
        return $this->moyquestsatisfmontreq3s0;
    }

    /**
     * Set moyquestsatisfmontreq4s0
     *
     * @param string $moyquestsatisfmontreq4s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfmontreq4s0($moyquestsatisfmontreq4s0)
    {
        $this->moyquestsatisfmontreq4s0 = $moyquestsatisfmontreq4s0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq4s0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq4s0()
    {
        return $this->moyquestsatisfmontreq4s0;
    }

    /**
     * Set moyquestsatisfmontreq5s0
     *
     * @param string $moyquestsatisfmontreq5s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfmontreq5s0($moyquestsatisfmontreq5s0)
    {
        $this->moyquestsatisfmontreq5s0 = $moyquestsatisfmontreq5s0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq5s0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq5s0()
    {
        return $this->moyquestsatisfmontreq5s0;
    }

    /**
     * Set moyquestsatisfmontreq6s0
     *
     * @param string $moyquestsatisfmontreq6s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfmontreq6s0($moyquestsatisfmontreq6s0)
    {
        $this->moyquestsatisfmontreq6s0 = $moyquestsatisfmontreq6s0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq6s0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq6s0()
    {
        return $this->moyquestsatisfmontreq6s0;
    }

    /**
     * Set moyquestsatisfpileq2s0
     *
     * @param string $moyquestsatisfpileq2s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfpileq2s0($moyquestsatisfpileq2s0)
    {
        $this->moyquestsatisfpileq2s0 = $moyquestsatisfpileq2s0;

        return $this;
    }

    /**
     * Get moyquestsatisfpileq2s0
     *
     * @return string 
     */
    public function getMoyquestsatisfpileq2s0()
    {
        return $this->moyquestsatisfpileq2s0;
    }

    /**
     * Set moyquestsatisfpileq3s0
     *
     * @param string $moyquestsatisfpileq3s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfpileq3s0($moyquestsatisfpileq3s0)
    {
        $this->moyquestsatisfpileq3s0 = $moyquestsatisfpileq3s0;

        return $this;
    }

    /**
     * Get moyquestsatisfpileq3s0
     *
     * @return string 
     */
    public function getMoyquestsatisfpileq3s0()
    {
        return $this->moyquestsatisfpileq3s0;
    }

    /**
     * Set moyquestsatisfpileq4s0
     *
     * @param string $moyquestsatisfpileq4s0
     * @return KpiWeek
     */
    public function setMoyquestsatisfpileq4s0($moyquestsatisfpileq4s0)
    {
        $this->moyquestsatisfpileq4s0 = $moyquestsatisfpileq4s0;

        return $this;
    }

    /**
     * Get moyquestsatisfpileq4s0
     *
     * @return string 
     */
    public function getMoyquestsatisfpileq4s0()
    {
        return $this->moyquestsatisfpileq4s0;
    }

}
