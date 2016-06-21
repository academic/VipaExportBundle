<?php

namespace Ojs\ExportBundle\Controller;

use APY\DataGridBundle\Grid\Source\Entity;
use Ojs\CoreBundle\Controller\OjsController as Controller;
use Ojs\JournalBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use APY\DataGridBundle\Grid\Action\MassAction;

class ArticleExportController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $translator = $this->get('translator');
        $grid = $this->get('grid');
        $journal = $this->get('ojs.journal_service')->getSelectedJournal();
        $source = new Entity(Article::class, 'export');
        $grid->setSource($source);

        $exportJsonAction = new MassAction($translator->trans('export.as.json'), [
            $this, 'jsonAction'
        ]);
        $grid->addMassAction($exportJsonAction);

        $exportXmlAction = new MassAction($translator->trans('export.as.xml'), [
            $this, 'xmlAction'
        ]);
        $grid->addMassAction($exportXmlAction);

        $exportCrossrefAction = new MassAction($translator->trans('export.as.crossref'), [
            $this, 'crossrefAction'
        ]);
        $grid->addMassAction($exportCrossrefAction);

        return $grid->getGridResponse('OjsExportBundle:ArticleExport:index.html.twig', [
            'grid'      => $grid,
            'journal'   => $journal,
        ]);
    }

    /**
     * @param $primaryKeys
     */
    public function jsonAction($primaryKeys)
    {
    }

    public function xmlAction()
    {

    }

    public function crossrefAction()
    {

    }
}
