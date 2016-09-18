<?php

namespace ED\GeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ED\GeneratorBundle\Form\EdType;
use ED\GeneratorBundle\Entity\GeneratorValidation;

class PageController extends Controller
{
    public $json = [];

    public function indexAction(Request $request)
    {

        $validation = new GeneratorValidation;
        $form = $this->createForm(EdType::class, $validation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $html = ($validation->getHtmlfile()) ? $this->get('ed.generator')->upload($validation->getHtmlfile()) : null;
            $css  = ($validation->getCssfile()) ? $this->get('ed.generator')->upload($validation->getCssfile()) : null;
            $js   = ($validation->getJsfile()) ? $this->get('ed.generator')->upload($validation->getJsfile()) : null;
            $this->json['projectname'] = $validation->getProjectname();

            if($form['check1']->getData())
            {
                $this->json['contact'] = $this->get('ed.generator_FormContact')->check($html);
            }

            $this->get('ed.generator')->jsoncreate($this->json);
        }
        return $this->render('EDGeneratorBundle:Page:index.html.twig', ['form' => $form->createView()]);
    }
}
