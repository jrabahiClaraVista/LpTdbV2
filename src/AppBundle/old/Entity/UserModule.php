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
 * Module
 *
 * @ORM\Table(name="app_user_module")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserModuleRepository")
 * @UniqueEntity(
 *     fields={"user", "module"},
 *     message="Duplicate entry for entries User - Module."
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class UserModule
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
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="userModules")
     * @ORM\JoinColumn(name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Module", inversedBy="userModules")
     * @ORM\JoinColumn(name="module_id", nullable=false)
     */
    private $module;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule(Module $module = null)
    {
        $this->module = $module;
        return $this;
    }




    /**
     * @ORM\PrePersist
     */
    public function increase()
    {
        $this->getModule()->increaseModule();
    }

    /**
     * @ORM\PreRemove
     */
    public function decrease()
    {
        $this->getModule()->decreaseModule();
    }

    /**
     * __toString
     * 
     * @return string
     */
    public function __toString() {
        return $this->getUser()->getUsername();
    }
}