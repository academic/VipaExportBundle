<?php

namespace Vipa\ExportBundle\Controller;

use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use GuzzleHttp\Client;
use Vipa\CoreBundle\Controller\VipaController as Controller;
use Vipa\JournalBundle\Entity\Article;
use Vipa\JournalBundle\Entity\Journal;
use Vipa\JournalBundle\Event\JournalEvent;
use Vipa\JournalBundle\Event\JournalItemEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use APY\DataGridBundle\Grid\Action\MassAction;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ArticleExportController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');
        $grid = $this->get('grid');
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $source = new Entity(Article::class, 'export');
        $grid->setSource($source);

        //setup mass actions
        $exportJsonAction = new MassAction($translator->trans('export.as.json'), [
            $this, 'massArticleJson'
        ]);
        $exportXmlAction = new MassAction($translator->trans('export.as.xml'), [
            $this, 'massArticleXml'
        ]);
        $exportCrossrefAction = new MassAction($translator->trans('export.as.crossref'), [
            $this, 'massArticleCrossref'
        ]);
        $exportPubmedAction = new MassAction($translator->trans('export.as.pubmed'), [
            $this, 'massArticlePubmed'
        ]);
        $grid->addMassAction($exportJsonAction);
        $grid->addMassAction($exportXmlAction);
        $grid->addMassAction($exportCrossrefAction);
        $grid->addMassAction($exportPubmedAction);

        //setup sing article export actions
        $exportGridAction = $this->get('export_grid_action');
        $actionColumn = new ActionsColumn("actions", 'actions');
        $rowAction[] = $exportGridAction->exportSingleArticleAsCrossref($journal->getId());
        $rowAction[] = $exportGridAction->exportSingleArticleAsJson($journal->getId());
        $rowAction[] = $exportGridAction->exportSingleArticleAsXml($journal->getId());
        $rowAction[] = $exportGridAction->exportSingleArticleAsPubmed($journal->getId());
        $actionColumn->setRowActions($rowAction);
        $grid->addColumn($actionColumn);

        return $grid->getGridResponse('VipaExportBundle:ArticleExport:index.html.twig', [
            'grid'      => $grid,
            'journal'   => $journal,
        ]);
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return BinaryFileResponse
     */
    public function singleArticleJsonAction(Request $request, Article $article)
    {
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($article->getJournal());
        $dataExport->setArticle($article);
        $jsonArticleData = $dataExport->articleToJson();
        $filePath = $dataExport->storeAsFile($jsonArticleData, 'json', $article->getId());
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
    public function massArticleJson($primaryKeys)
    {
        $journalService = $this->get('ojs.journal_service');
        if(count($primaryKeys) < 1){
            $this->errorFlashBag('you.must.select.one.least.element');
            return $this->redirectToRoute('ojs_data_export_user', [
                'journalId' => $journalService->getSelectedJournal()->getId(),
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        $articleRepo = $em->getRepository(Article::class);
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journalService->getSelectedJournal());
        $dataExport->setArticles($articleRepo->findById($primaryKeys));
        $jsonArticlesData = $dataExport->articlesToJson();
        $filePath = $dataExport->storeAsFile($jsonArticlesData, 'json', 'articles');
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
     * @param Article $article
     * @return BinaryFileResponse
     */
    public function singleArticleXmlAction(Request $request, Article $article)
    {
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($article->getJournal());
        $dataExport->setArticle($article);
        $xmlArticleData = $dataExport->articleToXml();
        $filePath = $dataExport->storeAsFile($xmlArticleData, 'xml', $article->getId());
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
    public function massArticleXml($primaryKeys)
    {
        $journalService = $this->get('ojs.journal_service');
        if(count($primaryKeys) < 1){
            $this->errorFlashBag('you.must.select.one.least.element');
            return $this->redirectToRoute('ojs_data_export_user', [
                'journalId' => $journalService->getSelectedJournal()->getId(),
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        $articleRepo = $em->getRepository(Article::class);
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journalService->getSelectedJournal());
        $dataExport->setArticles($articleRepo->findById($primaryKeys));
        $jsonArticlesData = $dataExport->articlesToXml();
        $filePath = $dataExport->storeAsFile($jsonArticlesData, 'xml', 'articles');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'xml');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return BinaryFileResponse
     */
    public function singleArticleCrossrefAction(Request $request, Article $article)
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journal);
        $dataExport->setArticle($article);

        $data = $this->setupCrossrefConfigs([
            'journal' => $journal,
            'articles' => [$article],
        ]);
        $crossrefCrossrefData = $this->renderView('VipaExportBundle:ArticleExport:crossref.xml.twig', $data);
        $filePath = $dataExport->storeAsFile($crossrefCrossrefData, 'xml', $article->getId());
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'crossref');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param $primaryKeys
     * @return BinaryFileResponse
     */
    public function massArticleCrossref($primaryKeys)
    {
        $journalService = $this->get('ojs.journal_service');
        if(count($primaryKeys) < 1){
            $this->errorFlashBag('you.must.select.one.least.element');
            return $this->redirectToRoute('ojs_data_export_user', [
                'journalId' => $journalService->getSelectedJournal()->getId(),
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        $articleRepo = $em->getRepository(Article::class);
        $journal = $journalService->getSelectedJournal();
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journal);
        $articles = $articleRepo->findById($primaryKeys);

        $data = $this->setupCrossrefConfigs([
            'journal' => $journal,
            'articles' => $articles,
        ]);
        $crossrefCrossrefData = $this->renderView('VipaExportBundle:ArticleExport:crossref.xml.twig', $data);
        $filePath = $dataExport->storeAsFile($crossrefCrossrefData, 'xml', 'articles');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'crossref');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function setupCrossrefConfigs($data)
    {
        $dispatcher = $this->get('event_dispatcher');
        $journalEvent = new JournalEvent($data['journal']);
        $dispatcher->dispatch('get.journal.crossref.config', $journalEvent);

        $articles = [];
        /** @var Article $article */
        foreach ($data['articles'] as $article){
            if(!empty($article->getDoi())){
                $articles[] = $article;
                continue;
            }
            $articleEvent = new JournalItemEvent($article);
            $dispatcher->dispatch('generate.article.doi', $articleEvent);
            $articles[] = $articleEvent->getItem();
        }

        $journal = $journalEvent->getJournal();

        $crossrefJournalTitle = '';

        if ($journal->getIssn()){
            try {
                $client = new Client();
                $response = $client->get('http://api.crossref.org/journals/'. $journal->getIssn());
                $crossrefJournal = json_decode($response->getBody()->getContents(), true);
                $crossrefJournalTitle = $crossrefJournal['message']['title'];
            } catch(\Exception $e) {
                $crossrefJournalTitle = '';
            }
        }

        return [
            'journal' => $journal,
            'articles' => $articles,
            'crossrefJournalTitle' => $crossrefJournalTitle
        ];
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return BinaryFileResponse
     */
    public function singleArticlePubmedAction(Request $request, Article $article)
    {
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journal);
        $dataExport->setArticle($article);
        $articlePubmedData = $this->renderView('VipaExportBundle:ArticleExport:pubmed.xml.twig', [
            'articles' => [$article],
            'journal' => $journal,
        ]);
        $filePath = $dataExport->storeAsFile($articlePubmedData, 'xml', $article->getId());
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'pubmed');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }

    /**
     * @param $primaryKeys
     * @return BinaryFileResponse
     */
    public function massArticlePubmed($primaryKeys)
    {
        $journalService = $this->get('ojs.journal_service');
        if(count($primaryKeys) < 1){
            $this->errorFlashBag('you.must.select.one.least.element');
            return $this->redirectToRoute('ojs_data_export_user', [
                'journalId' => $journalService->getSelectedJournal()->getId(),
            ]);
        }
        $em = $this->getDoctrine()->getManager();
        $articleRepo = $em->getRepository(Article::class);
        $journal = $journalService->getSelectedJournal();
        $dataExport = $this->get('ojs.data_export');
        $dataExport->setJournal($journal);
        $articles = $articleRepo->findById($primaryKeys);
        $articlePubmedData = $this->renderView('VipaExportBundle:ArticleExport:pubmed.xml.twig', [
            'articles' => $articles,
            'journal' => $journal,
        ]);
        $filePath = $dataExport->storeAsFile($articlePubmedData, 'xml', 'articles');
        $explode = explode('/', $filePath);
        $fileName = end($explode);
        $dataExport->addToHistory($filePath, 'pubmed');
        $file = $this->getParameter('kernel.root_dir'). '/../web/uploads/data_export/'.$filePath;
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        return $response;
    }
}
