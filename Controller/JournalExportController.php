<?php

namespace Ojs\ExportBundle\Controller;

use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Article;
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
        return $this->render('OjsExportBundle:JournalExport:index.html.twig');
    }

    /**
     * @return BinaryFileResponse
     */
    public function jsonAction()
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journal);
        $jsonJournalData = $dataExport->journalToJson();
        $filePath = $dataExport->storeAsFile($jsonJournalData, 'json');
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
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journal);
        $xmlJournalData = $dataExport->journalToXml();
        $filePath = $dataExport->storeAsFile($xmlJournalData, 'xml');
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
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journal);
        $articles = $em->getRepository(Article::class)->findAll();
        $doajJournalData = $this->renderView('OjsExportBundle:JournalExport:doaj.xml.twig', [
            'articles' => $articles,
        ]);
        $filePath = $dataExport->storeAsFile($doajJournalData, 'xml');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'doaj');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}
