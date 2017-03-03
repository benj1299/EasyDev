<?php

namespace ED\GeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ED\GeneratorBundle\Form\EdType;
use ED\GeneratorBundle\Entity\GeneratorValidation;
use ED\GeneratorBundle\Generator\Generator;

class PageController extends Controller
{
    public $json = [];
    public $check = ['check1' => 'ed_contact'];

    public function indexAction(Request $request)
    {
        //Création du formulaire dans la vue avec vérification
        $validation = new GeneratorValidation;
        $form = $this->createForm(EdType::class, $validation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $generator = new Generator($validation);

            //Génération du dossier de base
            $generator->baseGenerator();
            //Upload des fichiers
            $generator->upload();

            //Inserer une boucle pour tous les fichiers
            $bundlepath = $generator->addHtmlFile();

            //Ajout des options dans le json
            foreach ($this->check as $key => $name) {
                if($form["$key"]->getData()) { $this->json["$name"] = $generator->check($bundlepath, $name); }
            }

            $generator->addFunctions($this->json);
            //Fin de la boucle

            //Ajout des assets
            $generator->addAssetFile();

            $generator->InfosCreate($this->json);
        }
        return $this->render('EDGeneratorBundle:Page:index.html.twig', ['form' => $form->createView()]);
    }
}
