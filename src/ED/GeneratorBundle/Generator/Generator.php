<?php

namespace ED\GeneratorBundle\Generator;

use ED\GeneratorBundle\Generator\Options\FormContact;
use ED\TextParserBundle\TextParser\TextParser;
use ED\GeneratorBundle\Generator\Functions\generateSymfony;
use ED\GeneratorBundle\Entity\GeneratorValidation;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Generator
{
    protected static $id;
    protected $path;
    protected $infos;
    protected $projectname;
    protected $bundlename;
    protected $bundlepath;
    protected $fileCss;
    protected $fileJs;
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
        foreach ($this->infos->getfiles() as $files){
            $type = $this->upload($files);
            if ($type == "html")
            {
                $views = $this->addHtmlFile($files);
                $this->addFunctions($files, $this->checkOptionsValidity($views));
            }
            else { $this->addAssetFile(); }
        }
        $this->InfosCreate();
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
    private function upload(UploadedFile $file)
    {
        if ($file->guessExtension() == 'html') {
            $htmlFile = $file->getClientOriginalName();
            $file->move($this->path, $htmlFile);
            return "$this->path/$htmlFile";
        }
        elseif ($file->guessExtension() == 'css') {
            $cssFile = $file->getClientOriginalName();
            $file->move($this->path, $cssFile);
            return "$this->path/$cssFile";
        }
        elseif ($file->guessExtension() == 'js') {
            $jsFile = $file->getClientOriginalName();
            $file->move($this->path, $jsFile);
            return "$this->path/$jsFile";
        }
        else {
            // return une erreur
        }
    }

    /**
     * Ajoute les html dans les bundles
     * @return string
     */
    private function addHtmlFile($files) {
        $textParser = new TextParser;
        $symfony = new generateSymfony;

        $nameFile = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $files));

        if($files == 'index.html'){
            $symfony->createBaseTwigFile($this->path, $this->projectname, $files);
        }

        //Déplace le fichier dans les vues
        rename("$this->path/$files", "$this->bundlepath/Resources/views/$this->projectname/$files.twig");

        //Adaption du controller
        $textParser->replace_file("#Default#", $this->projectname, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $textParser->replace_file("#EdBundle#", $this->bundlename, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $textParser->replace_file("#extends Controller\n{#",
            "extends Controller {\n public function ".$nameFile."Action(){ return \$this->render('Main$this->bundlename:$this->projectname:$files.twig'); }",
            "$this->bundlepath/Controller/" . $this->projectname . "Controller.php");

        //Ecriture de la route
        $textParser->filewrite("$this->bundlepath/Resources/config/routing.yml", "main_$nameFile:\n\040\040\040\040path:     /\n\040\040\040\040defaults: { _controller: Main$this->bundlename:$this->projectname:$nameFile }\n");

        //Ecriture des vues
        $titre = $textParser->match_file_all("#<title>(.*)</title>#is", "$this->bundlepath/Resources/views/$this->projectname/$files.twig");
        $body = $textParser->match_file_all("#<body>(.*)</body>#is", "$this->bundlepath/Resources/views/$this->projectname/$files.twig");
        $titre = implode("",$titre[1]); $body = implode("", $body[1]);
        $textParser->replace_file("#(.*)#", "", "$this->bundlepath/Resources/views/$this->projectname/$files.twig");
        $textParser->filewrite("$this->bundlepath/Resources/views/$this->projectname/$files.twig", "{% extends 'Main$this->bundlename::layout.html.twig' %}\n{% block title %}$titre{% endblock %}\n{% block ".$this->projectname."_body %}$body{% endblock %}");

        return "$this->bundlepath/Resources/views/$this->projectname/$files.twig";
    }

    /**
     * Vérifie et ajoute les options dans l'array options[]
     */
    private function checkOptionsValidity(string $htmlFile){
        $options = [];
        foreach (self::CHECKOPTIONS as $key => $name) {
            if($this->infos->$key) { $options["$name"] = $htmlFile; }
        }
        return $options;
    }

    /**
     * Ajoute les assets dans les bundles
     */
    private function addAssetFile() {
        if($this->fileCss){rename("$this->path/$this->fileCss", "$this->bundlepath/Resources/public/css/$this->fileCss");}
        if($this->fileJs){rename("$this->path/$this->fileJs", "$this->bundlepath/Resources/public/js/$this->fileJs");}
    }

    /**
     * Ajoute les bundles au dossiers selon les options
     * @param array $json
     */
    private function addFunctions($files ,array $options)
    {
        $file = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $files));
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
     * Créé le json et Readme.md
     * @param array $json
     */
    private function InfosCreate(){
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