<?php

namespace Main\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller {
 public function indexAction(){ return $this->render('MainTestBundle:Test:index.html.twig'); }
}
