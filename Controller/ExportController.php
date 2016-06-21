<?php

namespace Ojs\ExportBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\ExportBundle\Entity\DataExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $source = new Entity('OjsExportBundle:DataExport');
        $grid = $this->get('grid')->setSource($source);
        $gridAction = $this->get('export_grid_action');
        $actionColumn = new ActionsColumn("actions", 'actions');
        $rowAction[] = $gridAction->exportDownload('ojs_data_export_download', [
            'id', 'journalId' => $journal->getId(),
            ]
        );
        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);

        return $grid->getGridResponse('OjsExportBundle:Export:history.html.twig', [
            'grid'      => $grid,
            'journal'   => $journal,
        ]);
    }

    /**
     * Export Download
     *
     * @param Request $request
     * @param DataExport $dataExport
     *
     * @return BinaryFileResponse
     */
    public function downloadAction(Request $request, DataExport $dataExport)
    {
        $explode = explode('/', $dataExport->getPath());
        $fileName = end($explode);
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$dataExport->getPath();
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}
