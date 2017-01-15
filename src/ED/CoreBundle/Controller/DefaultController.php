<?php

namespace ED\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EDCoreBundle:Default:index.html.twig');
    }
}
