<?php

namespace Ojs\DataBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Ojs\JournalBundle\Entity\Journal;
use Ojs\JournalBundle\Entity\JournalItemInterface;
use Prezent\Doctrine\Translatable\Annotation as Prezent;

/**
 * Class DataExport
 * @package Ojs\DataBundle\Entity
 * @GRID\Source(columns="id, type, createdAt")
 */
class DataExport implements JournalItemInterface
{
    /**
     * @var integer
     * @GRID\Column(title="id")
     */
    protected $id;

    /**
     * @var Journal
     */
    private $journal;

    /**
     * @var \DateTime
     * @GRID\Column(title="createdAt")
     */
    private $createdAt;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     * @GRID\Column(title="type")
     */
    private $type;

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     *
     * @param  Journal $journal
     * @return $this
     */
    public function setJournal(Journal $journal = null)
    {
        $this->journal = $journal;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function __toString()
    {
        return $this->getType();
    }
}
