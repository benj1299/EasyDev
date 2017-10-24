<?php

namespace ED\GeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ED\GeneratorBundle\Form\EdType;
use ED\GeneratorBundle\Entity\GeneratorValidation;
use ED\GeneratorBundle\Generator\Generator;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    public function indexAction(Request $request)
    {
        $validation = new GeneratorValidation;
        $form = $this->createForm(EdType::class, $validation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $generator = new Generator;
            $generator->call($validation);
            return $this->download($generator->getLink(), $generator->getProjectname());
        }
        return $this->render('EDGeneratorBundle:Page:index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Permet de télécharger le projet
     * @param $filename
     * @param $name
     * @return Response
     */
    public function download(string $filename, string $name){
        $response = new Response();

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'.zip";');
        $response->headers->set('Content-length', filesize($filename));

        $response->sendHeaders(); //Ne marche pas sur chrome à tester

        $response->setContent(file_get_contents($filename));
        return $response;
    }
}
