<?php

namespace Ojs\ExportBundle\Service;

use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ExportGridAction
 * @package Ojs\ExportBundle\Service
 */
class ExportGridAction
{
    /**
     * @var  TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    /**
     * @param string $route
     * @param $key
     * @return RowAction
     */
    public function exportDownload($route, $key = 'id')
    {
        $rowAction = new RowAction('<i class="fa fa-download"></i>', $route);
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-primary btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('ojs.export_download'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters($key);
        $rowAction->setRouteParametersMapping(['id' => 'id']);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleArticleAsJson($journalId)
    {
        $rowAction = new RowAction('JSON', 'ojs_data_export_single_article_json');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-primary btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.json'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleArticleAsXml($journalId)
    {
        $rowAction = new RowAction('XML', 'ojs_data_export_single_article_xml');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-warning btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.xml'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleArticleAsCrossref($journalId)
    {
        $rowAction = new RowAction('CrossRef', 'ojs_data_export_single_article_crossref');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-info btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.crossref'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleArticleAsPubmed($journalId)
    {
        $rowAction = new RowAction('Pubmed', 'ojs_data_export_single_article_pubmed');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-success btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.pubmed'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleIssueAsJson($journalId)
    {
        $rowAction = new RowAction('JSON', 'ojs_data_export_single_issue_json');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-primary btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.json'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleIssueAsXml($journalId)
    {
        $rowAction = new RowAction('XML', 'ojs_data_export_single_issue_xml');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-warning btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.xml'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleUserAsJson($journalId)
    {
        $rowAction = new RowAction('JSON', 'ojs_data_export_single_user_json');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-primary btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.json'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }

    /**
     * @param int $journalId
     * @return RowAction
     */
    public function exportSingleUserAsXml($journalId)
    {
        $rowAction = new RowAction('XML', 'ojs_data_export_single_user_xml');
        $rowAction->setAttributes(
            [
                'class' => 'btn btn-warning btn-xs ',
                'data-toggle' => 'tooltip',
                'title' => $this->translator->trans('export.as.xml'),
            ]
        );
        $rowAction->setTarget('_blank');
        $rowAction->setRouteParameters(['id', 'journalId' => $journalId]);
        return $rowAction;
    }
}
