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
}
