<?php

namespace ED\GeneratorBundle\Generator\Options;

use ED\GeneratorBundle\Generator\Generator;
use PHPHtmlParser\Dom;

class FormContact extends Generator {

    public function __construct(array $options)
    {
        foreach ($options as $file){
            $input = $this->analyseFile($file);
            $this->parsingFile($input, $file);
        }
    }

    /**
     * Analyse le fichier et retourne un tableau de sa composotion
     * @param string $file
     */
    private function analyseFile(string $file){
        $dom = new Dom;
        $dom->loadFromFile($file);
        return $input = [
                 "form"    => $dom->find('.contact-form'),
                 "name"    => $dom->find('.contact-name'),
                 "email"   => $dom->find('.contact-email'),
                 "subject" => $dom->find('.contact-subject'),
                 "message" => $dom->find('.contact-message')
        ];

    }

    /**
     * Modifie selon le tableau d'analyse le fichier et son environnement
     * @param array $information
     * @param string $file
     */
    private function parsingFile(array $input, string $file){

        if(isset($input["form"])){
            //créer tous les fichier de formulaire et tout.
        }

        //Faire boucle pour implémenter chaque fonction dans un champs.

    }

}