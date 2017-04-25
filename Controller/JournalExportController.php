<?php

namespace Vipa\ExportBundle\Controller;

use Vipa\CoreBundle\Controller\VipaController as Controller;
use Vipa\CoreBundle\Params\ArticleStatuses;
use Vipa\JournalBundle\Entity\Article;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class JournalExportController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('VipaExportBundle:JournalExport:index.html.twig');
    }

    /**
     * @return BinaryFileResponse
     */
    public function jsonAction()
    {
        $journal = $this->get('vipa.journal_service')->getSelectedJournal();
        $dataExport = $this->get('vipa.data_export');
        $dataExport->setJournal($journal);
        $jsonJournalData = $dataExport->journalToJson();
        $filePath = $dataExport->storeAsFile($jsonJournalData, 'json', $journal->getId());
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'json');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @return BinaryFileResponse
     */
    public function xmlAction()
    {
        $journal = $this->get('vipa.journal_service')->getSelectedJournal();
        $dataExport = $this->get('vipa.data_export');
        $dataExport->setJournal($journal);
        $xmlJournalData = $dataExport->journalToXml();
        $filePath = $dataExport->storeAsFile($xmlJournalData, 'xml', $journal->getId());
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'xml');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @return BinaryFileResponse
     */
    public function doajAction()
    {
        $em = $this->getDoctrine()->getManager();
        $journal = $this->get('vipa.journal_service')->getSelectedJournal();
        $dataExport = $this->get('vipa.data_export');
        $dataExport->setJournal($journal);
        $articles = $em->getRepository(Article::class)->findBy([
            'status' => ArticleStatuses::STATUS_PUBLISHED,
        ]);
        $doajJournalData = $this->renderView('VipaExportBundle:JournalExport:doaj.xml.twig', [
            'articles' => $articles,
        ]);
        $filePath = $dataExport->storeAsFile($doajJournalData, 'xml', $journal->getId());
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'doaj');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}
