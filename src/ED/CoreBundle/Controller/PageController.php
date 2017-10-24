<?php

namespace ED\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ED\GeneratorBundle\Form\EdType;
use ED\GeneratorBundle\Entity\GeneratorValidation;
use ED\GeneratorBundle\Generator\Generator;

class PageController extends Controller
{
    public function indexAction(Request $request)
    {
        $validation = new GeneratorValidation;
        $form = $this->createForm(EdType::class, $validation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $generator = new Generator;
            $generator->call($validation);
            return $this->get('_download')->execute($generator->getLink(), $generator->getProjectname());
        }
        return $this->render('EDGeneratorBundle:Page:index.html.twig', ['form' => $form->createView()]);
    }

}
