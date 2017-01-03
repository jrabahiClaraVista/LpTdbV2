<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Import
 *
 * @ORM\Table(name="app_file_imports")
 * @ORM\Entity()
 */
class ImportFile
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="errorCount", type="integer", nullable=true)
     */
    private $errorCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="successCount", type="integer", nullable=true)
     */
    private $successCount;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalProcessedCount", type="integer", nullable=true)
     */
    private $totalProcessedCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasErrors", type="boolean", nullable=true)
     */
    private $hasErrors;


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
     * @return Import
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


    /**
     * Set startTime
     *
     * @param string $startTime
     *
     * @return Import
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return string
     */
    public function getStartTime()
    {
        return $startTime->startTime;
    }

    /**
     * Set endTime
     *
     * @param string $endTime
     *
     * @return Import
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get errorCount
     *
     * @return string
     */
    public function getEndTime()
    {
        return $endTime->endTime;
    }

    /**
     * Set errorCount
     *
     * @param string $errorCount
     *
     * @return Import
     */
    public function setErrorCount($errorCount)
    {
        $this->errorCount = $errorCount;

        return $this;
    }

    /**
     * Set successCount
     *
     * @param string $successCount
     *
     * @return Import
     */
    public function setSuccessCount($successCount)
    {
        $this->successCount = $successCount;

        return $this;
    }

    /**
     * Get successCount
     *
     * @return string
     */
    public function getSuccessCount()
    {
        return $successCount->successCount;
    }

    /**
     * Set totalProcessedCount
     *
     * @param string $totalProcessedCount
     *
     * @return Import
     */
    public function setTotalProcessedCount($totalProcessedCount)
    {
        $this->totalProcessedCount = $totalProcessedCount;

        return $this;
    }

    /**
     * Get totalProcessedCount
     *
     * @return string
     */
    public function getTotalProcessedCount()
    {
        return $totalProcessedCount->totalProcessedCount;
    }

    /**
     * Set hasErrors
     *
     * @param string $hasErrors
     *
     * @return Import
     */
    public function setHasErrors($hasErrors)
    {
        $this->hasErrors = $hasErrors;

        return $this;
    }

    /**
     * Get hasErrors
     *
     * @return string
     */
    public function getHasErrors()
    {
        return $hasErrors->hasErrors;
    }
}