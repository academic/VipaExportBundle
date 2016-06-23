<?php

namespace Ojs\ExportBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\JournalUser;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use APY\DataGridBundle\Grid\Action\MassAction;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class UserExportController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $translator = $this->get('translator');
        $grid = $this->get('grid');
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $source = new Entity(JournalUser::class, 'export');
        $grid->setSource($source);

        //setup mass actions
        $exportJsonAction = new MassAction($translator->trans('export.as.json'), [
            $this, 'massUserJson'
        ]);
        $exportXmlAction = new MassAction($translator->trans('export.as.xml'), [
            $this, 'massUserXml'
        ]);
        $grid->addMassAction($exportJsonAction);
        $grid->addMassAction($exportXmlAction);

        //setup single user export actions
        $exportGridAction = $this->get('export_grid_action');
        $actionColumn = new ActionsColumn("actions", 'actions');
        $rowAction[] = $exportGridAction->exportSingleUserAsJson($journal->getId());
        $rowAction[] = $exportGridAction->exportSingleUserAsXml($journal->getId());
        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);

        return $grid->getGridResponse('OjsExportBundle:UserExport:index.html.twig', [
            'grid'      => $grid,
            'journal'   => $journal,
        ]);
    }

    /**
     * @param JournalUser $user
     * @return BinaryFileResponse
     */
    public function singleUserJsonAction(JournalUser $user)
    {
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($user->getJournal());
        $dataExport->setUser($user);
        $jsonUserData = $dataExport->userToJson();
        $filePath = $dataExport->storeAsFile($jsonUserData, 'json', $user->getId());
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
    public function massUserJson($primaryKeys)
    {
        $journalService = $this->get('ojs.journal_service');
        if(count($primaryKeys) < 1){
            $this->errorFlashBag('you.must.select.one.least.element');
            return $this->redirectToRoute('ojs_data_export_user', [
                'journalId' => $journalService->getSelectedJournal()->getId(),
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(JournalUser::class);
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journalService->getSelectedJournal());
        $dataExport->setUsers($userRepo->findById($primaryKeys));
        $jsonUsersData = $dataExport->usersToJson();
        $filePath = $dataExport->storeAsFile($jsonUsersData, 'json', 'users');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'json');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param JournalUser $user
     * @return BinaryFileResponse
     */
    public function singleUserXmlAction(JournalUser $user)
    {
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($user->getJournal());
        $dataExport->setUser($user);
        $xmlUserData = $dataExport->userToXml();
        $filePath = $dataExport->storeAsFile($xmlUserData, 'xml', $user->getId());
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
    public function massUserXml($primaryKeys = [])
    {
        $journalService = $this->get('ojs.journal_service');
        if(count($primaryKeys) < 1){
            $this->errorFlashBag('you.must.select.one.least.element');
            return $this->redirectToRoute('ojs_data_export_user', [
                'journalId' => $journalService->getSelectedJournal()->getId(),
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository(JournalUser::class);
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journalService->getSelectedJournal());
        $dataExport->setUsers($userRepo->findById($primaryKeys));
        $jsonUsersData = $dataExport->usersToXml();
        $filePath = $dataExport->storeAsFile($jsonUsersData, 'xml', 'users');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'xml');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}
