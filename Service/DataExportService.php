<?php

namespace Ojs\ExportBundle\Service;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializerBuilder;
use Ojs\CoreBundle\Helper\FileHelper;
use Ojs\JournalBundle\Entity\Article;
use Ojs\JournalBundle\Entity\Issue;
use Ojs\JournalBundle\Entity\Journal;
use JMS\Serializer\Serializer;
use Ojs\ExportBundle\Entity\DataExport;
use Ojs\JournalBundle\Entity\JournalUser;

/**
 * Class DataExportService
 * @package Ojs\ExportBundle\Service
 */
class DataExportService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Journal
     */
    private $journal = null;

    /**
     * @var string
     */
    private $kernelRootDir;

    /**
     * @var Article
     */
    private $article = null;

    /**
     * @var Article[]
     */
    private $articles = [];

    /**
     * @var Issue
     */
    private $issue = null;

    /**
     * @var Issue[]
     */
    private $issues = [];

    /**
     * @var JournalUser
     */
    private $user = null;

    /**
     * @var JournalUser[]
     */
    private $users = [];

    /**
     * JournalExportService constructor.
     *
     * @param EntityManager $em
     * @param $kernelRootDir
     */
    public function __construct(EntityManager $em, $kernelRootDir)
    {
        $this->serializer       = SerializerBuilder::create()->build();
        $this->kernelRootDir    = $kernelRootDir;
        $this->em               = $em;
    }

    /**
     * @param Journal $journal
     */
    public function setJournal(Journal $journal)
    {
        $this->journal = $journal;
    }

    /**
     * @param Article $article
     * @return $this
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @param Article[]|array $articles
     * @return $this
     */
    public function setArticles($articles = [])
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * @param Issue $issue
     * @return $this
     */
    public function setIssue(Issue $issue)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * @param Issue[]|array $issues
     * @return $this
     */
    public function setIssues($issues = [])
    {
        $this->issues = $issues;

        return $this;
    }

    /**
     * @param JournalUser $user
     * @return $this
     */
    public function setUser(JournalUser $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param JournalUser[]|array $users
     * @return $this
     */
    public function setUsers($users = [])
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function articleToJson()
    {
        if($this->article === null){
            throw new \LogicException('You must to specify article param');
        }
        return $this->serializer->serialize($this->article, 'json');
    }

    /**
     * @return mixed|string
     */
    public function articlesToJson()
    {
        if($this->articles === []){
            throw new \LogicException('You must to specify articles param');
        }
        return $this->serializer->serialize($this->articles, 'json');
    }

    /**
     * @return mixed|string
     */
    public function articleToXml()
    {
        if($this->article === null){
            throw new \LogicException('You must to specify article param');
        }
        return $this->serializer->serialize($this->article, 'xml');
    }

    /**
     * @return mixed|string
     */
    public function articlesToXml()
    {
        if($this->articles === []){
            throw new \LogicException('You must to specify articles param');
        }
        return $this->serializer->serialize($this->articles, 'xml');
    }

    /**
     * @return mixed|string
     */
    public function issueToJson()
    {
        if($this->issue === null){
            throw new \LogicException('You must to specify issue param');
        }
        return $this->serializer->serialize($this->issue, 'json');
    }

    /**
     * @return mixed|string
     */
    public function issuesToJson()
    {
        if($this->issues === []){
            throw new \LogicException('You must to specify issues param');
        }
        return $this->serializer->serialize($this->issues, 'json');
    }

    /**
     * @return mixed|string
     */
    public function issueToXml()
    {
        if($this->issue === null){
            throw new \LogicException('You must to specify issue param');
        }
        return $this->serializer->serialize($this->issue, 'xml');
    }

    /**
     * @return mixed|string
     */
    public function issuesToXml()
    {
        if($this->issues === []){
            throw new \LogicException('You must to specify issues param');
        }
        return $this->serializer->serialize($this->issues, 'xml');
    }

    /**
     * @return mixed|string
     */
    public function userToJson()
    {
        if($this->user === null){
            throw new \LogicException('You must to specify user param');
        }
        return $this->serializer->serialize($this->user, 'json');
    }

    /**
     * @return mixed|string
     */
    public function usersToJson()
    {
        if($this->users === []){
            throw new \LogicException('You must to specify users param');
        }
        return $this->serializer->serialize($this->users, 'json');
    }

    /**
     * @return mixed|string
     */
    public function userToXml()
    {
        if($this->user === null){
            throw new \LogicException('You must to specify user param');
        }
        return $this->serializer->serialize($this->user, 'xml');
    }

    /**
     * @return mixed|string
     */
    public function usersToXml()
    {
        if($this->users === []){
            throw new \LogicException('You must to specify users param');
        }
        return $this->serializer->serialize($this->users, 'xml');
    }

    /**
     * @return mixed|string
     */
    public function journalToXml()
    {
        if($this->journal === null){
            throw new \LogicException('You must to specify journal param');
        }
        return $this->serializer->serialize($this->journal, 'xml');
    }

    /**
     * @return mixed|string
     */
    public function journalToJson()
    {
        if($this->journal === null){
            throw new \LogicException('You must to specify journal param');
        }
        return $this->serializer->serialize($this->journal, 'json');
    }

    /**
     * @param string $content
     * @param string $type
     * @param string $fileName
     * @return string
     */
    public function storeAsFile($content, $type, $fileName)
    {
        $dataExportDir = $this->kernelRootDir . '/../web/uploads/data_export/';
        $fileHelper = new FileHelper();
        $generatePath = $fileHelper->generateRandomPath();
        if(!is_dir($dataExportDir.$generatePath)){
            mkdir($dataExportDir.$generatePath, 0775, true);
        }
        $filePath = $generatePath . $fileName.'.'.$type;
        file_put_contents($dataExportDir.$filePath, $content);

        return $filePath;
    }

    /**
     * @param $filePath
     * @param $type
     */
    public function addToHistory($filePath, $type)
    {
        $dataExport = new DataExport();
        $dataExport
            ->setCreatedAt(new \DateTime())
            ->setPath($filePath)
            ->setType($type)
            ->setJournal($this->journal)
        ;
        $this->em->persist($dataExport);
        $this->em->flush();

        return;
    }
}
