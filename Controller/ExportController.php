<?php

namespace Ojs\ExportBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Symfony\Component\HttpFoundation\Response;

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
        $rowAction[] = $gridAction->exportDownload('ojs_journal_export_download', [
            'id', 'journalId' => $journal->getId()
            ]
        );
        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);
        return $grid->getGridResponse('OjsExportBundle:Export:history.html.twig', [
            'grid'      => $grid,
            'journal'   => $journal,
        ]);
    }
}
