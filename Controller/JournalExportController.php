<?php

namespace Ojs\ExportBundle\Controller;

use Ojs\CoreBundle\Controller\OjsController as Controller;
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
        $journalExport = $this->get('ojs.data_export');
        $journalExport->setJournal($journal);
        $jsonJournalData = $journalExport->toJson();
        $filePath = $journalExport->storeAsFile($jsonJournalData, 'json');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $journalExport->addToHistory($filePath, 'json');
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
        $journalExport = $this->get('ojs.data_export');
        $journalExport->setJournal($journal);
        $xmlJournalData = $journalExport->toXml();
        $filePath = $journalExport->storeAsFile($xmlJournalData, 'xml');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $journalExport->addToHistory($filePath, 'xml');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    public function doajAction()
    {

    }
}
