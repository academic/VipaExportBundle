<?php

namespace Ojs\ExportBundle\Controller;

use Ojs\CoreBundle\Controller\OjsController as Controller;
use Symfony\Component\BrowserKit\Response;

class IssueExportController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('OjsExportBundle:IssueExport:index.html.twig');
    }
}
