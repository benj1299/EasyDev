<?php

namespace ED\GeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ED\GeneratorBundle\Form\EdType;
use ED\GeneratorBundle\Entity\GeneratorValidation;
use ED\GeneratorBundle\Generator\Generator;

class PageController extends Controller
{

    public function indexAction(Request $request)
    {
        //Création du formulaire dans la vue avec vérification
        $validation = new GeneratorValidation;
        $form = $this->createForm(EdType::class, $validation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $generator = new Generator;
            $generator->call($validation);
        }
        return $this->render('EDGeneratorBundle:Page:index.html.twig', ['form' => $form->createView()]);
    }
}
