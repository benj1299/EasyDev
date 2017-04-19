<?php

namespace ED\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EDAdminBundle:Default:index.html.twig');
    }
}
