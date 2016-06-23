<?php

namespace Ojs\ExportBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Issue;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use APY\DataGridBundle\Grid\Action\MassAction;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class IssueExportController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');
        $grid = $this->get('grid');
        $cache = $this->get('array_cache');
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $source = new Entity(Issue::class, 'export');
        $source->manipulateRow(
            function (Row $row) use ($request, $cache) {
                /** @var Issue $entity */
                $entity = $row->getEntity();
                if (!is_null($entity)) {
                    $entity->setDefaultLocale($request->getDefaultLocale());
                    if($cache->contains('grid_row_id_'.$entity->getId())){
                        $row->setClass('hidden');
                    }else{
                        $cache->save('grid_row_id_'.$entity->getId(), true);
                        $row->setField('translations.title', $entity->getTitleTranslations());
                    }
                }

                return $row;
            }
        );
        $grid->setSource($source);

        //setup mass actions
        $exportJsonAction = new MassAction($translator->trans('export.as.json'), [
            $this, 'massIssueJson'
        ]);
        $exportXmlAction = new MassAction($translator->trans('export.as.xml'), [
            $this, 'massIssueXml'
        ]);
        $grid->addMassAction($exportJsonAction);
        $grid->addMassAction($exportXmlAction);

        //setup single issue export actions
        $exportGridAction = $this->get('export_grid_action');
        $actionColumn = new ActionsColumn("actions", 'actions');
        $rowAction[] = $exportGridAction->exportSingleIssueAsJson($journal->getId());
        $rowAction[] = $exportGridAction->exportSingleIssueAsXml($journal->getId());
        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);

        return $grid->getGridResponse('OjsExportBundle:IssueExport:index.html.twig', [
            'grid'      => $grid,
            'journal'   => $journal,
        ]);
    }

    /**
     * @param Request $request
     * @param Issue $issue
     * @return BinaryFileResponse
     */
    public function singleIssueJsonAction(Request $request, Issue $issue)
    {
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($issue->getJournal());
        $dataExport->setIssue($issue);
        $jsonIssueData = $dataExport->issueToJson();
        $filePath = $dataExport->storeAsFile($jsonIssueData, 'json', $issue->getId());
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'json');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param $primaryKeys
     * @return BinaryFileResponse
     */
    public function massIssueJson($primaryKeys)
    {
        $em = $this->getDoctrine()->getManager();
        $issueRepo = $em->getRepository(Issue::class);
        $journalService = $this->get('ojs.journal_service');
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journalService->getSelectedJournal());
        $dataExport->setIssues($issueRepo->findById($primaryKeys));
        $jsonIssuesData = $dataExport->issuesToJson();
        $filePath = $dataExport->storeAsFile($jsonIssuesData, 'json', 'issues');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'json');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param Request $request
     * @param Issue $issue
     * @return BinaryFileResponse
     */
    public function singleIssueXmlAction(Request $request, Issue $issue)
    {
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($issue->getJournal());
        $dataExport->setIssue($issue);
        $xmlIssueData = $dataExport->issueToXml();
        $filePath = $dataExport->storeAsFile($xmlIssueData, 'xml', $issue->getId());
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'xml');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param $primaryKeys
     * @return BinaryFileResponse
     */
    public function massIssueXml($primaryKeys)
    {
        $em = $this->getDoctrine()->getManager();
        $issueRepo = $em->getRepository(Issue::class);
        $journalService = $this->get('ojs.journal_service');
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journalService->getSelectedJournal());
        $dataExport->setIssues($issueRepo->findById($primaryKeys));
        $jsonIssuesData = $dataExport->issuesToXml();
        $filePath = $dataExport->storeAsFile($jsonIssuesData, 'xml', 'issues');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'xml');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}
