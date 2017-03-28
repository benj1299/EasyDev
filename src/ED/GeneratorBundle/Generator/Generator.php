<?php

namespace ED\GeneratorBundle\Generator;

use ED\GeneratorBundle\Generator\Options\FormContact;
use ED\TextParserBundle\TextParser\TextParser;
use ED\GeneratorBundle\Generator\Functions\generateSymfony;
use ED\GeneratorBundle\Entity\GeneratorValidation;

class Generator
{
    protected static $id;
    protected $path;
    protected $infos;
    protected $projectname;
    protected $bundlename;
    protected $bundlepath;
    protected $fileHtml;
    protected $fileCss;
    protected $fileJs;
    public $options = [];
    const CHECKOPTIONS = ['check1' => 'ed_contact'];

    /**
     * Démarre l'algorithme et initialise les fonctions et variables
     * @param $infos
     */
    public function call(GeneratorValidation $infos){
        self::$id = bin2hex(openssl_random_pseudo_bytes(random_int(2, 5)));
        $this->infos = $infos;
        $this->projectname = strip_tags(addslashes(ucfirst($this->infos->projectname)));
        $this->path = "../tmp/".$this->projectname."_".self::$id;
        $this->bundlename = $this->projectname."Bundle";
        $this->bundlepath = "$this->path/$this->projectname/src/Main/$this->bundlename";

        $this->baseGenerator();
        $this->upload();
        //Inserer une boucle pour tous les fichiers
        $htmlFile = $this->addHtmlFile();
        $this->addAssetFile();
        $this->checkOptionsValidity($htmlFile);
        $this->addFunctions($this->options);
        //Fin de la boucle
        $this->InfosCreate($this->options);
    }

    /**
     * Génère le dossier de base à configurer
     */
    private function baseGenerator(){
            $symfony = new generateSymfony;
            $symfony->createBase($this->path, $this->projectname);

        if (is_dir("$this->path/$this->projectname")) {
            $symfony->bundleCreate($this->path, $this->projectname);
            $symfony->projectRename($this->path, $this->projectname, $this->bundlepath);
            $symfony->layoutCreate($this->projectname, $this->bundlepath);
            $symfony->writeBundleFile($this->path, $this->projectname, $this->bundlename);
        }
    }

    /**
     * Upload les fichiers
     * @return string
     */
    private function upload()
    {
        $file = $this->infos->getfiles();

        if ($file->guessExtension() == 'html') {
            $this->fileHtml = $file->getClientOriginalName();
            $file->move($this->path, $this->fileHtml);
            return "$this->path/$this->fileHtml";
        }
        elseif ($file->guessExtension() == 'css') {
            $this->fileCss = $file->getClientOriginalName();
            $file->move($this->path, $this->fileCss);
            return "$this->path/$this->fileCss";
        }
        elseif ($file->guessExtension() == 'js') {
            $this->fileJs = $file->getClientOriginalName();
            $file->move($this->path, $this->fileJs);
            return "$this->path/$this->fileJs";
        }
        else {
            // return une erreur
        }
    }

    /**
     * Ajoute les html dans les bundles
     * @return string
     */
    public function addHtmlFile() {
        $textParser = new TextParser;
        $symfony = new generateSymfony;

        $file = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $this->fileHtml));

        if($this->fileHtml == 'index.html'){
            $symfony->createBaseTwigFile($this->path, $this->projectname, $this->fileHtml);
        }

        //Déplace le fichier dans les vues
        rename("$this->path/$this->fileHtml", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");

        //Adaption du controller
        $textParser->replace_file("#Default#", $this->projectname, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $textParser->replace_file("#EdBundle#", $this->bundlename, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $textParser->replace_file("#extends Controller\n{#",
            "extends Controller {\n public function ".$file."Action(){ return \$this->render('Main$this->bundlename:$this->projectname:$this->fileHtml.twig'); }",
            "$this->bundlepath/Controller/" . $this->projectname . "Controller.php");

        //Ecriture de la route
        $textParser->filewrite("$this->bundlepath/Resources/config/routing.yml", "main_$file:\n\040\040\040\040path:     /\n\040\040\040\040defaults: { _controller: Main$this->bundlename:$this->projectname:$file }\n");

        //Ecriture des vues
        $titre = $textParser->match_file_all("#<title>(.*)</title>#is", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");
        $body = $textParser->match_file_all("#<body>(.*)</body>#is", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");
        $titre = implode("",$titre[1]); $body = implode("", $body[1]);
        $textParser->replace_file("#(.*)#", "", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");
        $textParser->filewrite("$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig", "{% extends 'Main$this->bundlename::layout.html.twig' %}\n{% block title %}$titre{% endblock %}\n{% block ".$this->projectname."_body %}$body{% endblock %}");

        return "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig";
    }

    /**
     * Ajoute et vérifie les options dans l'array options[]
     */
    private function checkOptionsValidity(string $htmlFile){
        foreach (self::CHECKOPTIONS as $key => $name) {
            if($this->infos->$key) { $this->options["$name"] = $htmlFile; }
        }
    }

    /**
     * Ajoute les bundles au dossiers selon les options
     * @param array $json
     */
    private function addFunctions(array $options)
    {
        $file = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $this->fileHtml));
        if(!empty($options['ed_contact']) && $options['ed_contact'] === true)
        {
            new FormContact($this->bundlepath, $file);
        }
        if(!empty($options['ed_fos_admin']) && $options['ed_fos_admin'] === true)
        {
            new FOSAdmin($this->bundlepath, $file);
        }
    }

    /**
     * Ajoute les assets dans les bundles
     */
    private function addAssetFile() {
        if($this->fileCss){rename("$this->path/$this->fileCss", "$this->bundlepath/Resources/public/$this->fileCss");}
        if($this->fileJs){rename("$this->path/$this->fileJs", "$this->bundlepath/Resources/public/$this->fileJs");}
    }

    /**
     * Créé le json et Readme.md
     * @param array $json
     */
    private function InfosCreate(array $json){
        $json['projectname'] = $this->projectname;
        //Créé le json
        $file = fopen("$this->path/config_".self::$id.".json", 'w+');
        fputs($file, json_encode($json));
        fclose($file);
        //Créé le Readme.md
        $todo = "cmd :\r\n
        composer update\r\n
        php bin/console cache:clear\r\n
        php bin/console assets:install\r\n
        php bin/console server:run\r\n";
        $file = fopen("$this->path/TODO.txt", 'w+');
        fputs($file, $todo);
        fclose($file);
    }

}