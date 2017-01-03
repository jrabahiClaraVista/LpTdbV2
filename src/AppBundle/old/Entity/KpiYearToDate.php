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
 * KpiYeartoDate
 *
 * @ORM\Table(name="app_kpi_ytd")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\KpiYeartoDateRepository")
 * @UniqueEntity(fields={"user_id", "date"})
 * @ORM\HasLifecycleCallbacks()
 */
class KpiYearToDate
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
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="kpiYearToDates")
     */
    private $user;
    /**
     * @var string
     * @ORM\Column(name="username", type="string", nullable=true)
     *
     */
    private $username;

    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_transac_ytd", type="integer")
     */
    private $nbTransacYtd;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npe_ytd", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpeYtd;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nve_ytd", type="decimal", precision=9, scale=2)
     */
    private $txTransacNveYtd;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npes_ytd", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesYtd;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nves_ytd", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesYtd;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_npesa_ytd", type="decimal", precision=9, scale=2)
     */
    private $txTransacNpesaYtd;

    /**
     * @var decimal
     *
     * @ORM\Column(name="tx_transac_nvesa_ytd", type="decimal", precision=9, scale=2)
     */
    private $txTransacNvesaYtd;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npe_ytd", type="integer")
     */
    private $rankNpeYtd = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npes_ytd", type="integer")
     */
    private $rankNpesYtd = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank_npesa_ytd", type="integer")
     */
    private $rankNpesaYtd = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="ca_crm_ytd", type="integer")
     */
    private $caCrmYtd;


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
     * @return Campaign
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
     * Set nbTransacYtd
     *
     * @param integer $nbTransacYtd
     * @return KpiYearToDate
     */
    public function setNbTransacYtd($nbTransacYtd)
    {
        $this->nbTransacYtd = $nbTransacYtd;

        return $this;
    }

    /**
     * Get nbTransacYtd
     *
     * @return integer 
     */
    public function getNbTransacYtd()
    {
        return $this->nbTransacYtd;
    }

    /**
     * Set txTransacNpeYtd
     *
     * @param decimal $txTransacNpeYtd
     * @return KpiYearToDate
     */
    public function setTxTransacNpeYtd($txTransacNpeYtd)
    {
        $this->txTransacNpeYtd = $txTransacNpeYtd;

        return $this;
    }

    /**
     * Get txTransacNpeYtd
     *
     * @return decimal 
     */
    public function getTxTransacNpeYtd()
    {
        return $this->txTransacNpeYtd;
    }

    /**
     * Set txTransacNveYtd
     *
     * @param decimal $txTransacNveYtd
     * @return KpiYearToDate
     */
    public function setTxTransacNveYtd($txTransacNveYtd)
    {
        $this->txTransacNveYtd = $txTransacNveYtd;

        return $this;
    }

    /**
     * Get txTransacNveYtd
     *
     * @return decimal 
     */
    public function getTxTransacNveYtd()
    {
        return $this->txTransacNveYtd;
    }

    /**
     * Set txTransacNpesYtd
     *
     * @param decimal $txTransacNpesYtd
     * @return KpiYearToDate
     */
    public function setTxTransacNpesYtd($txTransacNpesYtd)
    {
        $this->txTransacNpesYtd = $txTransacNpesYtd;

        return $this;
    }

    /**
     * Get txTransacNpesYtd
     *
     * @return decimal 
     */
    public function getTxTransacNpesYtd()
    {
        return $this->txTransacNpesYtd;
    }

    /**
     * Set txTransacNvesYtd
     *
     * @param decimal $txTransacNvesYtd
     * @return KpiYearToDate
     */
    public function setTxTransacNvesYtd($txTransacNvesYtd)
    {
        $this->txTransacNvesYtd = $txTransacNvesYtd;

        return $this;
    }

    /**
     * Get txTransacNvesYtd
     *
     * @return decimal 
     */
    public function getTxTransacNvesYtd()
    {
        return $this->txTransacNvesYtd;
    }

    /**
     * Set txTransacNpesaYtd
     *
     * @param decimal $txTransacNpesaYtd
     * @return KpiYearToDate
     */
    public function setTxTransacNpesaYtd($txTransacNpesaYtd)
    {
        $this->txTransacNpesaYtd = $txTransacNpesaYtd;

        return $this;
    }

    /**
     * Get txTransacNpesaYtd
     *
     * @return decimal 
     */
    public function getTxTransacNpesaYtd()
    {
        return $this->txTransacNpesaYtd;
    }

    /**
     * Set txTransacNvesaYtd
     *
     * @param decimal $txTransacNvesaYtd
     * @return KpiYearToDate
     */
    public function setTxTransacNvesaYtd($txTransacNvesaYtd)
    {
        $this->txTransacNvesaYtd = $txTransacNvesaYtd;

        return $this;
    }

    /**
     * Get txTransacNvesaYtd
     *
     * @return decimal 
     */
    public function getTxTransacNvesaYtd()
    {
        return $this->txTransacNvesaYtd;
    }

    /**
     * Set rankNpeYtd
     *
     * @param integer $rankNpeYtd
     * @return KpiYearToDate
     */
    public function setRankNpeYtd($rankNpeYtd)
    {
        $this->rankNpeYtd = $rankNpeYtd;

        return $this;
    }

    /**
     * Get rankNpeYtd
     *
     * @return integer 
     */
    public function getRankNpeYtd()
    {
        return $this->rankNpeYtd;
    }


    /**
     * Set rankNpesYtd
     *
     * @param integer $rankNpesYtd
     * @return KpiYearToDate
     */
    public function setRankNpesYtd($rankNpesYtd)
    {
        $this->rankNpesYtd = $rankNpesYtd;

        return $this;
    }

    /**
     * Get rankNpesYtd
     *
     * @return integer 
     */
    public function getRankNpesYtd()
    {
        return $this->rankNpesYtd;
    }

    /**
     * Set rankNpesaYtd
     *
     * @param integer $rankNpesaYtd
     * @return KpiYearToDate
     */
    public function setRankNpesaYtd($rankNpesaYtd)
    {
        $this->rankNpesaYtd = $rankNpesaYtd;

        return $this;
    }

    /**
     * Get rankNpesaYtd
     *
     * @return integer 
     */
    public function getRankNpesaYtd()
    {
        return $this->rankNpesaYtd;
    }

    /**
     * Set caCrmYtd
     *
     * @param integer $caCrmYtd
     * @return KpiYearToDate
     */
    public function setCaCrmYtd($caCrmYtd)
    {
        $this->caCrmYtd = $caCrmYtd;

        return $this;
    }

    /**
     * Get caCrmYtd
     *
     * @return integer 
     */
    public function getCaCrmYtd()
    {
        return $this->caCrmYtd;
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
