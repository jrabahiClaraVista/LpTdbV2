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
 * KpiTrimestre
 *
 * @ORM\Table(name="app_kpi_trim", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="UNIQUE_USER_DATE", columns={"user_id", "date"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Entity\KpiTrimRepository")
 * @UniqueEntity(fields={"user_id", "date"})
 * @ORM\HasLifecycleCallbacks()
 */
class KpiTrim
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

    /*
     *
     *  KPI TRIM
     *
     */
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nb_transac_T0", type="integer")
     */
    private $nbTransacT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_linked_T0", type="decimal", precision=9, scale=2)
     */
    private $txtransaclinkedT0;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npe_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeT0;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nve_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNveT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npes_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nves_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesa_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvesa_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesaT0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npe_T0", type="integer")
     */
    private $rankNpeT0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npes_T0", type="integer")
     */
    private $rankNpesT0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npesa_T0", type="integer")
     */
    private $rankNpesaT0 = 1;


    /**
     * Nouvelle variables Avril 2019
     */

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_linked_optin_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacLinkedOptinT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_linked_optout_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacLinkedOptoutT0;


    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npei_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeiT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npeo_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeoT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesi_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesiT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npeso_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesoT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesai_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaiT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesao_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaoT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nps_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpsT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npsi_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpsiT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npso_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpsoT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvs_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvsT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npa_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpaT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npai_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpaiT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npao_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpaoT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nva_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvaT0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npes2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpes2T0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesi2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesi2T0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npeso2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeso2T0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nves2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNves2T0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesa2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesa2T0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesai2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesai2T0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesao2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesao2T0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvesa2_T0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesa2T0;


    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npe2_T0", type="integer")
     */
    private $rankNpe2T0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_nps2_T0", type="integer")
     */
    private $rankNps2T0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npes2_T0", type="integer")
     */
    private $rankNpes2T0 = 1;

    /*
     *
     *  KPI SATISFACTION
     *
     */

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_questsatisf_t0", type="integer")
     */
    private $nbrequestsatisft0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_questsatisf_montred_t0", type="integer")
     */
    private $nbrequestsatisfmontredt0;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_questsatisf_piled_t0", type="integer")
     */
    private $nbrequestsatisfpiledt0;

    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_quest_satisf_promoteur_t0", type="decimal", precision=9, scale=2)
     */
    private $txquestsatisfpromoteurdt0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_quest_satisf_passif_t0", type="decimal", precision=9, scale=2)
     */
    private $txquestsatisfpassift0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_quest_satisf_detracteur_t0", type="decimal", precision=9, scale=2)
     */
    private $txquestsatisfdetracteurt0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="quest_satisf_nps_t0", type="decimal", precision=9, scale=2)
     */
    private $questsatisfnpst0;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="quest_satisf_rank_nps_t0", type="integer")
     */
    private $questsatisfranknpst0;


    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q2_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq2t0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q3_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq3t0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q4_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq4t0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q5_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq5t0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_montre_q6_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfmontreq6t0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_pile_q2_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfpileq2t0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_pile_q3_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfpileq3t0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="moy_quest_satisf_pile_q4_t0", type="decimal", precision=9, scale=2)
     */
    private $moyquestsatisfpileq4t0;

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
     * Set nbTransacT0
     *
     * @param integer $nbTransacT0
     * @return KpiMonth
     */
    public function setNbTransacT0($nbTransacT0)
    {
        $this->nbTransacT0 = $nbTransacT0;

        return $this;
    }

    /**
     * Get nbTransacT0
     *
     * @return integer 
     */
    public function getNbTransacT0()
    {
        return $this->nbTransacT0;
    }

    /**
     * Set txTransacNpeT0
     *
     * @param decimal $txTransacNpeT0
     * @return KpiMonth
     */
    public function setTxTransacNpeT0($txTransacNpeT0)
    {
        $this->txTransacNpeT0 = $txTransacNpeT0;

        return $this;
    }

    /**
     * Get txTransacNpeT0
     *
     * @return decimal 
     */
    public function getTxTransacNpeT0()
    {
        return $this->txTransacNpeT0;
    }

    /**
     * Set txtransaclinkedT0
     *
     * @param decimal $txtransaclinkedT0
     * @return KpiMonth
     */
    public function setTxTransacLinkedT0($txtransaclinkedT0)
    {
        $this->txtransaclinkedT0 = $txtransaclinkedT0;

        return $this;
    }

    /**
     * Get txtransaclinkedT0
     *
     * @return decimal 
     */
    public function getTxTransacLinkedT0()
    {
        return $this->txtransaclinkedT0;
    }

    /**
     * Set txTransacNveT0
     *
     * @param decimal $txTransacNveT0
     * @return KpiMonth
     */
    public function setTxTransacNveT0($txTransacNveT0)
    {
        $this->txTransacNveT0 = $txTransacNveT0;

        return $this;
    }

    /**
     * Get txTransacNveT0
     *
     * @return decimal 
     */
    public function getTxTransacNveT0()
    {
        return $this->txTransacNveT0;
    }

    /**
     * Set txTransacNpesT0
     *
     * @param decimal $txTransacNpesT0
     * @return KpiMonth
     */
    public function setTxTransacNpesT0($txTransacNpesT0)
    {
        $this->txTransacNpesT0 = $txTransacNpesT0;

        return $this;
    }

    /**
     * Get txTransacNpesT0
     *
     * @return decimal 
     */
    public function getTxTransacNpesT0()
    {
        return $this->txTransacNpesT0;
    }

    /**
     * Set txTransacNvesT0
     *
     * @param decimal $txTransacNvesT0
     * @return KpiMonth
     */
    public function setTxTransacNvesT0($txTransacNvesT0)
    {
        $this->txTransacNvesT0 = $txTransacNvesT0;

        return $this;
    }

    /**
     * Get txTransacNvesT0
     *
     * @return decimal 
     */
    public function getTxTransacNvesT0()
    {
        return $this->txTransacNvesT0;
    }

    /**
     * Set txTransacNpesaT0
     *
     * @param decimal $txTransacNpesaT0
     * @return KpiMonth
     */
    public function setTxTransacNpesaT0($txTransacNpesaT0)
    {
        $this->txTransacNpesaT0 = $txTransacNpesaT0;

        return $this;
    }

    /**
     * Get txTransacNpesaT0
     *
     * @return decimal 
     */
    public function getTxTransacNpesaT0()
    {
        return $this->txTransacNpesaT0;
    }

    /**
     * Set txTransacNvesaT0
     *
     * @param decimal $txTransacNvesaT0
     * @return KpiMonth
     */
    public function setTxTransacNvesaT0($txTransacNvesaT0)
    {
        $this->txTransacNvesaT0 = $txTransacNvesaT0;

        return $this;
    }

    /**
     * Get txTransacNvesaT0
     *
     * @return decimal 
     */
    public function getTxTransacNvesaT0()
    {
        return $this->txTransacNvesaT0;
    }

    /**
     * Set rankNpeT0
     *
     * @param string $rankNpeT0
     * @return KpiMonth
     */
    public function setRankNpeT0($rankNpeT0)
    {
        $this->rankNpeT0 = $rankNpeT0;

        return $this;
    }

    /**
     * Get rankNpeT0
     *
     * @return string 
     */
    public function getRankNpeT0()
    {
        return $this->rankNpeT0;
    }

    /**
     * Set rankNpesT0
     *
     * @param string $rankNpesT0
     * @return KpiMonth
     */
    public function setRankNpesT0($rankNpesT0)
    {
        $this->rankNpesT0 = $rankNpesT0;

        return $this;
    }

    /**
     * Get rankNpesT0
     *
     * @return string 
     */
    public function getRankNpesT0()
    {
        return $this->rankNpesT0;
    }

    /**
     * Set rankNpesaT0
     *
     * @param string $rankNpesaT0
     * @return KpiMonth
     */
    public function setRankNpesaT0($rankNpesaT0)
    {
        $this->rankNpesaT0 = $rankNpesaT0;

        return $this;
    }

    /**
     * Get rankNpesaT0
     *
     * @return string 
     */
    public function getRankNpesaT0()
    {
        return $this->rankNpesaT0;
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



    public function getTxTransacNpeiT0()
    {
        return $this->txTransacNpeiT0;
    }
     
    public function setTxTransacNpeiT0($txTransacNpeiT0)
    {
        $this->txTransacNpeiT0 = $txTransacNpeiT0;
        return $this;
    }
    public function getTxTransacNpeoT0()
    {
        return $this->txTransacNpeoT0;
    }
     
    public function setTxTransacNpeoT0($txTransacNpeoT0)
    {
        $this->txTransacNpeoT0 = $txTransacNpeoT0;
        return $this;
    }
    public function getTxTransacNpesiT0()
    {
        return $this->txTransacNpesiT0;
    }
     
    public function setTxTransacNpesiT0($txTransacNpesiT0)
    {
        $this->txTransacNpesiT0 = $txTransacNpesiT0;
        return $this;
    }
    public function getTxTransacNpesoT0()
    {
        return $this->txTransacNpesoT0;
    }
     
    public function setTxTransacNpesoT0($txTransacNpesoT0)
    {
        $this->txTransacNpesoT0 = $txTransacNpesoT0;
        return $this;
    }
    public function getTxTransacNpesaiT0()
    {
        return $this->txTransacNpesaiT0;
    }
     
    public function setTxTransacNpesaiT0($txTransacNpesaiT0)
    {
        $this->txTransacNpesaiT0 = $txTransacNpesaiT0;
        return $this;
    }
    public function getTxTransacNpesaoT0()
    {
        return $this->txTransacNpesaoT0;
    }
     
    public function setTxTransacNpesaoT0($txTransacNpesaoT0)
    {
        $this->txTransacNpesaoT0 = $txTransacNpesaoT0;
        return $this;
    }
    public function getTxTransacNpsT0()
    {
        return $this->txTransacNpsT0;
    }
     
    public function setTxTransacNpsT0($txTransacNpsT0)
    {
        $this->txTransacNpsT0 = $txTransacNpsT0;
        return $this;
    }
    public function getTxTransacNpsiT0()
    {
        return $this->txTransacNpsiT0;
    }
     
    public function setTxTransacNpsiT0($txTransacNpsiT0)
    {
        $this->txTransacNpsiT0 = $txTransacNpsiT0;
        return $this;
    }
    public function getTxTransacNpsoT0()
    {
        return $this->txTransacNpsoT0;
    }
     
    public function setTxTransacNpsoT0($txTransacNpsoT0)
    {
        $this->txTransacNpsoT0 = $txTransacNpsoT0;
        return $this;
    }
    public function getTxTransacNvsT0()
    {
        return $this->txTransacNvsT0;
    }
     
    public function setTxTransacNvsT0($txTransacNvsT0)
    {
        $this->txTransacNvsT0 = $txTransacNvsT0;
        return $this;
    }
    public function getTxTransacNpaT0()
    {
        return $this->txTransacNpaT0;
    }
     
    public function setTxTransacNpaT0($txTransacNpaT0)
    {
        $this->txTransacNpaT0 = $txTransacNpaT0;
        return $this;
    }
    public function getTxTransacNpaiT0()
    {
        return $this->txTransacNpaiT0;
    }
     
    public function setTxTransacNpaiT0($txTransacNpaiT0)
    {
        $this->txTransacNpaiT0 = $txTransacNpaiT0;
        return $this;
    }
    public function getTxTransacNpaoT0()
    {
        return $this->txTransacNpaoT0;
    }
     
    public function setTxTransacNpaoT0($txTransacNpaoT0)
    {
        $this->txTransacNpaoT0 = $txTransacNpaoT0;
        return $this;
    }
    public function getTxTransacNvaT0()
    {
        return $this->txTransacNvaT0;
    }
     
    public function setTxTransacNvaT0($txTransacNvaT0)
    {
        $this->txTransacNvaT0 = $txTransacNvaT0;
        return $this;
    }
    public function getTxTransacNpes2T0()
    {
        return $this->txTransacNpes2T0;
    }
     
    public function setTxTransacNpes2T0($txTransacNpes2T0)
    {
        $this->txTransacNpes2T0 = $txTransacNpes2T0;
        return $this;
    }
    public function getTxTransacNpesi2T0()
    {
        return $this->txTransacNpesi2T0;
    }
     
    public function setTxTransacNpesi2T0($txTransacNpesi2T0)
    {
        $this->txTransacNpesi2T0 = $txTransacNpesi2T0;
        return $this;
    }
    public function getTxTransacNpeso2T0()
    {
        return $this->txTransacNpeso2T0;
    }
     
    public function setTxTransacNpeso2T0($txTransacNpeso2T0)
    {
        $this->txTransacNpeso2T0 = $txTransacNpeso2T0;
        return $this;
    }
    public function getTxTransacNves2T0()
    {
        return $this->txTransacNves2T0;
    }
     
    public function setTxTransacNves2T0($txTransacNves2T0)
    {
        $this->txTransacNves2T0 = $txTransacNves2T0;
        return $this;
    }
    public function getTxTransacNpesa2T0()
    {
        return $this->txTransacNpesa2T0;
    }
     
    public function setTxTransacNpesa2T0($txTransacNpesa2T0)
    {
        $this->txTransacNpesa2T0 = $txTransacNpesa2T0;
        return $this;
    }
    public function getTxTransacNpesai2T0()
    {
        return $this->txTransacNpesai2T0;
    }
     
    public function setTxTransacNpesai2T0($txTransacNpesai2T0)
    {
        $this->txTransacNpesai2T0 = $txTransacNpesai2T0;
        return $this;
    }
    public function getTxTransacNpesao2T0()
    {
        return $this->txTransacNpesao2T0;
    }
     
    public function setTxTransacNpesao2T0($txTransacNpesao2T0)
    {
        $this->txTransacNpesao2T0 = $txTransacNpesao2T0;
        return $this;
    }
    public function getTxTransacNvesa2T0()
    {
        return $this->txTransacNvesa2T0;
    }
     
    public function setTxTransacNvesa2T0($txTransacNvesa2T0)
    {
        $this->txTransacNvesa2T0 = $txTransacNvesa2T0;
        return $this;
    }


    public function getTxTransacLinkedOptinT0()
    {
        return $this->txTransacLinkedOptinT0;
    }
     
    public function setTxTransacLinkedOptinT0($txTransacLinkedOptinT0)
    {
        $this->txTransacLinkedOptinT0 = $txTransacLinkedOptinT0;
        return $this;
    }


    public function getTxTransacLinkedOptoutT0()
    {
        return $this->txTransacLinkedOptoutT0;
    }
     
    public function setTxTransacLinkedOptoutT0($txTransacLinkedOptoutT0)
    {
        $this->txTransacLinkedOptoutT0 = $txTransacLinkedOptoutT0;
        return $this;
    }


    /**
     * Set rankNpe2T0
     *
     * @param string $rankNpe2T0
     * @return KpiMonth
     */
    public function setRankNpe2T0($rankNpe2T0)
    {
        $this->rankNpe2T0 = $rankNpe2T0;

        return $this;
    }

    /**
     * Get rankNpe2T0
     *
     * @return string 
     */
    public function getRankNpe2T0()
    {
        return $this->rankNpe2T0;
    }

    /**
     * Set rankNps2T0
     *
     * @param string $rankNps2T0
     * @return KpiMonth
     */
    public function setRankNps2T0($rankNps2T0)
    {
        $this->rankNps2T0 = $rankNps2T0;

        return $this;
    }

    /**
     * Get rankNps2T0
     *
     * @return string 
     */
    public function getRankNps2T0()
    {
        return $this->rankNps2T0;
    }

    /**
     * Set rankNpes2T0
     *
     * @param string $rankNpes2T0
     * @return KpiMonth
     */
    public function setRankNpes2T0($rankNpes2T0)
    {
        $this->rankNpes2T0 = $rankNpes2T0;

        return $this;
    }

    /**
     * Get rankNpes2T0
     *
     * @return string 
     */
    public function getRankNpes2T0()
    {
        return $this->rankNpes2T0;
    }

    /**
     * Set nbrequestsatisft0
     *
     * @param integer $nbrequestsatisft0
     * @return KpiTrim
     */
    public function setNbrequestsatisft0($nbrequestsatisft0)
    {
        $this->nbrequestsatisft0 = $nbrequestsatisft0;

        return $this;
    }

    /**
     * Get nbrequestsatisft0
     *
     * @return integer 
     */
    public function getNbrequestsatisft0()
    {
        return $this->nbrequestsatisft0;
    }

    /**
     * Set nbrequestsatisfmontredt0
     *
     * @param integer $nbrequestsatisfmontredt0
     * @return KpiTrim
     */
    public function setNbrequestsatisfmontredt0($nbrequestsatisfmontredt0)
    {
        $this->nbrequestsatisfmontredt0 = $nbrequestsatisfmontredt0;

        return $this;
    }

    /**
     * Get nbrequestsatisfmontredt0
     *
     * @return integer 
     */
    public function getNbrequestsatisfmontredt0()
    {
        return $this->nbrequestsatisfmontredt0;
    }

    /**
     * Set nbrequestsatisfpiledt0
     *
     * @param integer $nbrequestsatisfpiledt0
     * @return KpiTrim
     */
    public function setNbrequestsatisfpiledt0($nbrequestsatisfpiledt0)
    {
        $this->nbrequestsatisfpiledt0 = $nbrequestsatisfpiledt0;

        return $this;
    }

    /**
     * Get nbrequestsatisfpiledt0
     *
     * @return integer 
     */
    public function getNbrequestsatisfpiledt0()
    {
        return $this->nbrequestsatisfpiledt0;
    }

    /**
     * Set txquestsatisfpromoteurdt0
     *
     * @param integer $txquestsatisfpromoteurdt0
     * @return KpiTrim
     */
    public function setTxquestsatisfpromoteurdt0($txquestsatisfpromoteurdt0)
    {
        $this->txquestsatisfpromoteurdt0 = $txquestsatisfpromoteurdt0;

        return $this;
    }

    /**
     * Get txquestsatisfpromoteurdt0
     *
     * @return integer 
     */
    public function getTxquestsatisfpromoteurdt0()
    {
        return $this->txquestsatisfpromoteurdt0;
    }

    /**
     * Set txquestsatisfpassift0
     *
     * @param string $txquestsatisfpassift0
     * @return KpiTrim
     */
    public function setTxquestsatisfpassift0($txquestsatisfpassift0)
    {
        $this->txquestsatisfpassift0 = $txquestsatisfpassift0;

        return $this;
    }

    /**
     * Get txquestsatisfpassift0
     *
     * @return string 
     */
    public function getTxquestsatisfpassift0()
    {
        return $this->txquestsatisfpassift0;
    }

    /**
     * Set txquestsatisfdetracteurt0
     *
     * @param string $txquestsatisfdetracteurt0
     * @return KpiTrim
     */
    public function setTxquestsatisfdetracteurt0($txquestsatisfdetracteurt0)
    {
        $this->txquestsatisfdetracteurt0 = $txquestsatisfdetracteurt0;

        return $this;
    }

    /**
     * Get txquestsatisfdetracteurt0
     *
     * @return string 
     */
    public function getTxquestsatisfdetracteurt0()
    {
        return $this->txquestsatisfdetracteurt0;
    }

    /**
     * Set questsatisfnpst0
     *
     * @param string $questsatisfnpst0
     * @return KpiTrim
     */
    public function setQuestsatisfnpst0($questsatisfnpst0)
    {
        $this->questsatisfnpst0 = $questsatisfnpst0;

        return $this;
    }

    /**
     * Get questsatisfnpst0
     *
     * @return string 
     */
    public function getQuestsatisfnpst0()
    {
        return $this->questsatisfnpst0;
    }

    /**
     * Set questsatisfranknpst0
     *
     * @param integer $questsatisfranknpst0
     * @return KpiTrim
     */
    public function setQuestsatisfranknpst0($questsatisfranknpst0)
    {
        $this->questsatisfranknpst0 = $questsatisfranknpst0;

        return $this;
    }

    /**
     * Get questsatisfranknpst0
     *
     * @return integer 
     */
    public function getQuestsatisfranknpst0()
    {
        return $this->questsatisfranknpst0;
    }

    /**
     * Set moyquestsatisfmontreq2t0
     *
     * @param string $moyquestsatisfmontreq2t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfmontreq2t0($moyquestsatisfmontreq2t0)
    {
        $this->moyquestsatisfmontreq2t0 = $moyquestsatisfmontreq2t0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq2t0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq2t0()
    {
        return $this->moyquestsatisfmontreq2t0;
    }

    /**
     * Set moyquestsatisfmontreq3t0
     *
     * @param string $moyquestsatisfmontreq3t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfmontreq3t0($moyquestsatisfmontreq3t0)
    {
        $this->moyquestsatisfmontreq3t0 = $moyquestsatisfmontreq3t0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq3t0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq3t0()
    {
        return $this->moyquestsatisfmontreq3t0;
    }

    /**
     * Set moyquestsatisfmontreq4t0
     *
     * @param string $moyquestsatisfmontreq4t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfmontreq4t0($moyquestsatisfmontreq4t0)
    {
        $this->moyquestsatisfmontreq4t0 = $moyquestsatisfmontreq4t0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq4t0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq4t0()
    {
        return $this->moyquestsatisfmontreq4t0;
    }

    /**
     * Set moyquestsatisfmontreq5t0
     *
     * @param string $moyquestsatisfmontreq5t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfmontreq5t0($moyquestsatisfmontreq5t0)
    {
        $this->moyquestsatisfmontreq5t0 = $moyquestsatisfmontreq5t0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq5t0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq5t0()
    {
        return $this->moyquestsatisfmontreq5t0;
    }

    /**
     * Set moyquestsatisfmontreq6t0
     *
     * @param string $moyquestsatisfmontreq6t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfmontreq6t0($moyquestsatisfmontreq6t0)
    {
        $this->moyquestsatisfmontreq6t0 = $moyquestsatisfmontreq6t0;

        return $this;
    }

    /**
     * Get moyquestsatisfmontreq6t0
     *
     * @return string 
     */
    public function getMoyquestsatisfmontreq6t0()
    {
        return $this->moyquestsatisfmontreq6t0;
    }

    /**
     * Set moyquestsatisfpileq2t0
     *
     * @param string $moyquestsatisfpileq2t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfpileq2t0($moyquestsatisfpileq2t0)
    {
        $this->moyquestsatisfpileq2t0 = $moyquestsatisfpileq2t0;

        return $this;
    }

    /**
     * Get moyquestsatisfpileq2t0
     *
     * @return string 
     */
    public function getMoyquestsatisfpileq2t0()
    {
        return $this->moyquestsatisfpileq2t0;
    }

    /**
     * Set moyquestsatisfpileq3t0
     *
     * @param string $moyquestsatisfpileq3t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfpileq3t0($moyquestsatisfpileq3t0)
    {
        $this->moyquestsatisfpileq3t0 = $moyquestsatisfpileq3t0;

        return $this;
    }

    /**
     * Get moyquestsatisfpileq3t0
     *
     * @return string 
     */
    public function getMoyquestsatisfpileq3t0()
    {
        return $this->moyquestsatisfpileq3t0;
    }

    /**
     * Set moyquestsatisfpileq4t0
     *
     * @param string $moyquestsatisfpileq4t0
     * @return KpiTrim
     */
    public function setMoyquestsatisfpileq4t0($moyquestsatisfpileq4t0)
    {
        $this->moyquestsatisfpileq4t0 = $moyquestsatisfpileq4t0;

        return $this;
    }

    /**
     * Get moyquestsatisfpileq4t0
     *
     * @return string 
     */
    public function getMoyquestsatisfpileq4t0()
    {
        return $this->moyquestsatisfpileq4t0;
    }
}
