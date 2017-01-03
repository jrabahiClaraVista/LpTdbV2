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
 * @ORM\Table(name="app_kpi_month")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\KpiMonthRepository")
 * @UniqueEntity(fields={"user_id", "date"})
 * @ORM\HasLifecycleCallbacks()
 */
class KpiMonth
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
     * @ORM\Column(name="username", type="string", nullable=true)
     *
     */
    private $username;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="kpiMonths")
     */
    private $user;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="nb_transac_m0", type="integer")
     */
    private $nbTransacM0;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npe_m0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeM0;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nve_m0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNveM0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npes_m0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesM0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nves_m0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesM0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesa_m0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaM0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvesa_m0", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesaM0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npe_m0", type="integer")
     */
    private $rankNpeM0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npes_m0", type="integer")
     */
    private $rankNpesM0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npesa_m0", type="integer")
     */
    private $rankNpesaM0 = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_clients_contactables_email_h", type="integer")
     */
    private $nbreClientsContactablesEmailH;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_clients_inactifs_email_h", type="integer")
     */
    private $nbreClientsInactifsEmailH;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_clients_animes_m0", type="integer")
     */
    private $nbreClientsAnimesM0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbre_clients_transformes_m0", type="integer")
     */
    private $nbreClientsTransformesM0;

    /**
     * @var integer
     *
     * @ORM\Column(name="CA_clients_transformes_m0", type="integer")
     */
    private $caClientsTransformesM0;

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
     * Set username
     *
     * @param string $username
     *
     * @return kpiMonth
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nbTransacM0
     *
     * @param integer $nbTransacM0
     * @return KpiMonth
     */
    public function setNbTransacM0($nbTransacM0)
    {
        $this->nbTransacM0 = $nbTransacM0;

        return $this;
    }

    /**
     * Get nbTransacM0
     *
     * @return integer 
     */
    public function getNbTransacM0()
    {
        return $this->nbTransacM0;
    }

    /**
     * Set txTransacNpeM0
     *
     * @param decimal $txTransacNpeM0
     * @return KpiMonth
     */
    public function setTxTransacNpeM0($txTransacNpeM0)
    {
        $this->txTransacNpeM0 = $txTransacNpeM0;

        return $this;
    }

    /**
     * Get txTransacNpeM0
     *
     * @return decimal 
     */
    public function getTxTransacNpeM0()
    {
        return $this->txTransacNpeM0;
    }

    /**
     * Set txTransacNveM0
     *
     * @param decimal $txTransacNveM0
     * @return KpiMonth
     */
    public function setTxTransacNveM0($txTransacNveM0)
    {
        $this->txTransacNveM0 = $txTransacNveM0;

        return $this;
    }

    /**
     * Get txTransacNveM0
     *
     * @return decimal 
     */
    public function getTxTransacNveM0()
    {
        return $this->txTransacNveM0;
    }

    /**
     * Set txTransacNpesM0
     *
     * @param decimal $txTransacNpesM0
     * @return KpiMonth
     */
    public function setTxTransacNpesM0($txTransacNpesM0)
    {
        $this->txTransacNpesM0 = $txTransacNpesM0;

        return $this;
    }

    /**
     * Get txTransacNpesM0
     *
     * @return decimal 
     */
    public function getTxTransacNpesM0()
    {
        return $this->txTransacNpesM0;
    }

    /**
     * Set txTransacNvesM0
     *
     * @param decimal $txTransacNvesM0
     * @return KpiMonth
     */
    public function setTxTransacNvesM0($txTransacNvesM0)
    {
        $this->txTransacNvesM0 = $txTransacNvesM0;

        return $this;
    }

    /**
     * Get txTransacNvesM0
     *
     * @return decimal 
     */
    public function getTxTransacNvesM0()
    {
        return $this->txTransacNvesM0;
    }

    /**
     * Set txTransacNpesaM0
     *
     * @param decimal $txTransacNpesaM0
     * @return KpiMonth
     */
    public function setTxTransacNpesaM0($txTransacNpesaM0)
    {
        $this->txTransacNpesaM0 = $txTransacNpesaM0;

        return $this;
    }

    /**
     * Get txTransacNpesaM0
     *
     * @return decimal 
     */
    public function getTxTransacNpesaM0()
    {
        return $this->txTransacNpesaM0;
    }

    /**
     * Set txTransacNvesaM0
     *
     * @param decimal $txTransacNvesaM0
     * @return KpiMonth
     */
    public function setTxTransacNvesaM0($txTransacNvesaM0)
    {
        $this->txTransacNvesaM0 = $txTransacNvesaM0;

        return $this;
    }

    /**
     * Get txTransacNvesaM0
     *
     * @return decimal 
     */
    public function getTxTransacNvesaM0()
    {
        return $this->txTransacNvesaM0;
    }

    /**
     * Set rankNpeM0
     *
     * @param string $rankNpeM0
     * @return KpiMonth
     */
    public function setRankNpeM0($rankNpeM0)
    {
        $this->rankNpeM0 = $rankNpeM0;

        return $this;
    }

    /**
     * Get rankNpeM0
     *
     * @return string 
     */
    public function getRankNpeM0()
    {
        return $this->rankNpeM0;
    }

    /**
     * Set rankNpesM0
     *
     * @param string $rankNpesM0
     * @return KpiMonth
     */
    public function setRankNpesM0($rankNpesM0)
    {
        $this->rankNpesM0 = $rankNpesM0;

        return $this;
    }

    /**
     * Get rankNpesM0
     *
     * @return string 
     */
    public function getRankNpesM0()
    {
        return $this->rankNpesM0;
    }

    /**
     * Set rankNpesaM0
     *
     * @param string $rankNpesaM0
     * @return KpiMonth
     */
    public function setRankNpesaM0($rankNpesaM0)
    {
        $this->rankNpesaM0 = $rankNpesaM0;

        return $this;
    }

    /**
     * Get rankNpesaM0
     *
     * @return string 
     */
    public function getRankNpesaM0()
    {
        return $this->rankNpesaM0;
    }

    /**
     * Set nbreClientsContactablesEmailH
     *
     * @param integer $nbreClientsContactablesEmailH
     * @return KpiMonth
     */
    public function setNbreClientsContactablesEmailH($nbreClientsContactablesEmailH)
    {
        $this->nbreClientsContactablesEmailH = $nbreClientsContactablesEmailH;

        return $this;
    }

    /**
     * Get nbreClientsContactablesEmailH
     *
     * @return integer 
     */
    public function getNbreClientsContactablesEmailH()
    {
        return $this->nbreClientsContactablesEmailH;
    }

    /**
     * Set nbreClientsInactifsEmailH
     *
     * @param integer $nbreClientsInactifsEmailH
     * @return KpiMonth
     */
    public function setNbreClientsInactifsEmailH($nbreClientsInactifsEmailH)
    {
        $this->nbreClientsInactifsEmailH = $nbreClientsInactifsEmailH;

        return $this;
    }

    /**
     * Get nbreClientsInactifsEmailH
     *
     * @return integer 
     */
    public function getNbreClientsInactifsEmailH()
    {
        return $this->nbreClientsInactifsEmailH;
    }

    /**
     * Set nbreClientsAnimesM0
     *
     * @param integer $nbreClientsAnimesM0
     * @return KpiMonth
     */
    public function setNbreClientsAnimesM0($nbreClientsAnimesM0)
    {
        $this->nbreClientsAnimesM0 = $nbreClientsAnimesM0;

        return $this;
    }

    /**
     * Get nbreClientsAnimesM0
     *
     * @return integer 
     */
    public function getNbreClientsAnimesM0()
    {
        return $this->nbreClientsAnimesM0;
    }

    /**
     * Set nbreClientsTransformesM0
     *
     * @param integer $nbreClientsTransformesM0
     * @return KpiMonth
     */
    public function setNbreClientsTransformesM0($nbreClientsTransformesM0)
    {
        $this->nbreClientsTransformesM0 = $nbreClientsTransformesM0;

        return $this;
    }

    /**
     * Get nbreClientsTransformesM0
     *
     * @return integer 
     */
    public function getNbreClientsTransformesM0()
    {
        return $this->nbreClientsTransformesM0;
    }

    /**
     * Set caClientsTransformesM0
     *
     * @param integer $caClientsTransformesM0
     * @return KpiMonth
     */
    public function setCaClientsTransformesM0($caClientsTransformesM0)
    {
        $this->caClientsTransformesM0 = $caClientsTransformesM0;

        return $this;
    }

    /**
     * Get caClientsTransformesM0
     *
     * @return integer 
     */
    public function getCaClientsTransformesM0()
    {
        return $this->caClientsTransformesM0;
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