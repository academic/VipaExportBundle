<?php

namespace Ojs\ExportBundle\Controller;

use Ojs\CoreBundle\Controller\OjsController as Controller;
use Symfony\Component\HttpFoundation\Response;

class UserExportController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('OjsExportBundle:UserExport:index.html.twig');
    }
}
