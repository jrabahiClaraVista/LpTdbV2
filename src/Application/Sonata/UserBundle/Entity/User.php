<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;

use AppBundle\Entity\KpiMonth;
use AppBundle\Entity\KpiYearToDate;
use AppBundle\Entity\Module;
use AppBundle\Entity\UserModule;

/**
 * This file has been generated by the Sonata EasyExtends bundle.
 *
 * @link https://sonata-project.org/bundles/easy-extends
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */

/**
 * User
 *
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $webMail
     *
     * @ORM\Column(name="web_mail", type="string", length=100, nullable=true)
     */
    protected $webMail;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\KpiMonth", mappedBy="user")
     */
    private $kpiMonths;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\KpiYearToDate", mappedBy="user")
     */
    private $kpiYearToDates;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserModule", mappedBy="user")
     * @ORM\JoinTable(name="app_user_module")
     */
    private $userModules;

    /**
     * @var string $dr
     *
     * @ORM\Column(name="dr", type="string", length=100, nullable=true)
     */
    protected $dr;

    /**
     * @var string $brand
     *
     * @ORM\Column(name="brand", type="string", length=100, nullable=true)
     */
    protected $brand ;

    /**
     * @var string $role
     *
     * @ORM\Column(name="role", type="string", length=100, nullable=true)
     */
    protected $role ;


    public function __construct()
    {
        parent::__construct();

        $this->kpiMonths = new ArrayCollection();
        $this->kpiYearToDates = new ArrayCollection();
        $this->userModules = new ArrayCollection();
        $dr = "";
        $brand = "";
        $role = "";

        $this->password = $this->setPassword("claravista123");
        $this->enabled = true;
    }

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * set id
     *
     * @return int $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get webMail
     *
     * @return int $webMail
     */
    public function getWebMail()
    {
        return $this->webMail;
    }

    /**
     * Set webMail
     *
     * @param string $webMail
     *
     * @return User
     */
    public function setWebMail($webMail)
    {
        $this->webMail = $webMail;
        return $this;
    }

    /**
     * Get dr
     *
     * @return int $dr
     */
    public function getDr()
    {
        return $this->dr;
    }

    /**
     * Set dr
     *
     * @param string $dr
     *
     * @return User
     */
    public function setDr($dr)
    {
        $this->dr = $dr;
        return $this;
    }

    /**
     * Get brand
     *
     * @return int $brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set brand
     *
     * @param string $brand
     *
     * @return User
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * Get role
     *
     * @return int $role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get emailTarget
     *
     * @return int $emailTarget
     */
    public function getEmailTarget()
    {
        return $this->emailTarget;
    }

    /**
     * Set emailTarget
     *
     * @param string $emailTarget
     *
     * @return User
     */
    public function setEmailTarget($emailTarget)
    {
        $this->emailTarget = $emailTarget;
        return $this;
    }

    /**
     * add kpiMonth
     * remove kpiMonth
     * get kpiMonths
     *
     * @param KpiMonth $kpiMonth
     *
     * @return User
     */
    public function addKpiMonth(KpiMonth $kpiMonth)
    {
        $this->kpiMonths[] = $kpiMonth;

        return $this;
    }

    public function removeKpiMonth(KpiMonth $kpiMonth)
    {
        $this->kpiMonths->removeElement($kpiMonth);
    }

    public function getKpiMonths()
    {
        return $this->kpiMonths;
    }

    /**
     * add kpiYearToDate
     * remove kpiYearToDate
     * get kpiYearToDates
     *
     * @param KpiYearToDate $kpiYearToDate
     *
     * @return User
     */
    public function addKpiYearToDate(KpiYearToDate $kpiYearToDate)
    {
        $this->kpiYearToDates[] = $kpiYearToDate;

        return $this;
    }

    public function removeKpiYearToDate(KpiYearToDate $kpiYearToDate)
    {
        $this->kpiYearToDates->removeElement($kpiYearToDate);
    }

    public function getKpiYearToDates()
    {
        return $this->kpiYearToDates;
    }

    /**
     * add userModule
     * remove userModule
     * get userModules
     *
     * @param UserModule $userModule
     *
     * @return User
     */
    public function addUserModule(UserModule $userModule)
    {
        $this->userModules[] = $userModule;

        return $this;
    }

    public function removeUserModule(UserModule $userModule)
    {
        $this->userModules->removeElement($userModule);
    }

    public function getUserModules()
    {
        return $this->userModules;
    }
}
