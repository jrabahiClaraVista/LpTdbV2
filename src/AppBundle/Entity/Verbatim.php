<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Verbatim
 *
 * @ORM\Table(name="app_verbatim")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VerbatimRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Verbatim
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
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="marque", type="string", length=255, nullable=true)
     */
    private $marque;
    
    /**
     * @var string
     *
     * @ORM\Column(name="dr", type="string", length=255, nullable=true)
     */
    private $dr;
    
    /**
     * @var string
     *
     * @ORM\Column(name="boutique", type="string", length=255, nullable=true)
     */
    private $boutique;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="question", type="integer", nullable=true)
     */
    private $question;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="note", type="integer", nullable=true)
     */
    private $note;
    
    /**
     * @var text
     *
     * @ORM\Column(name="verbatim", type="text", nullable=true)
     */
    private $verbatim;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

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
     * Set type
     *
     * @param string $type
     * @return Verbatim
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set marque
     *
     * @param string $marque
     * @return Verbatim
     */
    public function setMarque($marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque
     *
     * @return string 
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set dr
     *
     * @param string $dr
     * @return Verbatim
     */
    public function setDr($dr)
    {
        $this->dr = $dr;

        return $this;
    }

    /**
     * Get dr
     *
     * @return string 
     */
    public function getDr()
    {
        return $this->dr;
    }

    /**
     * Set boutique
     *
     * @param string $boutique
     * @return Verbatim
     */
    public function setBoutique($boutique)
    {
        $this->boutique = $boutique;

        return $this;
    }

    /**
     * Get boutique
     *
     * @return string 
     */
    public function getBoutique()
    {
        return $this->boutique;
    }

    /**
     * Set question
     *
     * @param integer $question
     * @return Verbatim
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return integer 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set note
     *
     * @param integer $note
     * @return Verbatim
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return integer 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set verbatim
     *
     * @param string $verbatim
     * @return Verbatim
     */
    public function setVerbatim($verbatim)
    {
        $this->verbatim = $verbatim;

        return $this;
    }

    /**
     * Get verbatim
     *
     * @return string 
     */
    public function getVerbatim()
    {
        return $this->verbatim;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Verbatim
     */
    public function setDate($date)
    {
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
}
