<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Module
 *
 * @ORM\Table(name="app_module")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Module
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
     * @ORM\Column(name="nb_modules", type="integer")
     */
    private $nbModules = 0;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserModule", mappedBy="module")
     */
    private $userModules;

    public function __construct()
    {
        $this->userModules = new ArrayCollection();
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Campaign
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

    public function addUserModules(UserModule $userModule)
    {
        $this->userModules[] = $userModule;
        return $this;
    }

    public function removeUserModules(UserModule $userModule)
    {
        $this->userModules->removeElement($userModule);
    }

    public function getUserModules()
    {
        return $this->userModules;
    }

    /**
     * Get nbModules
     *
     * @return integer 
     */
    public function getNbModules()
    {
        return $this->nbModules;
    }

    public function increaseModule()
    {
        $this->nbModules++;
    }

    public function decreaseModule()
    {
        $this->nbModules--;
    }

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }
     
}