<?php

namespace Ojs\ExportBundle\Controller;

use Ojs\CoreBundle\Controller\OjsController as Controller;
use Symfony\Component\HttpFoundation\Response;

class JournalExportController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('OjsExportBundle:JournalExport:index.html.twig');
    }
}
