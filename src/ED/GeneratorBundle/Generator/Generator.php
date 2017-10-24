<?php

namespace ED\GeneratorBundle\Generator;

use ED\GeneratorBundle\Generator\Options\FormContact;
use ED\TextParserBundle\TextParser\TextParser;
use ED\GeneratorBundle\Generator\Library\generateSymfony;
use ED\GeneratorBundle\Entity\GeneratorValidation;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Generator
{
    protected $id;
    protected $path;
    protected $infos;
    protected $projectname;
    protected $bundlename;
    protected $bundlepath;
    protected $configApp = [];
    protected $listUpload = [];
    const CHECKOPTIONS = ['check1' => 'form-contact'];

    /**
     * Démarre l'algorithme et initialise les fonctions et variables
     * @param $infos
     */
    public function call(GeneratorValidation $infos){
        $this->infos = $infos;
        $this->id = bin2hex(openssl_random_pseudo_bytes(random_int(2, 5)));
        $this->projectname = strip_tags(addslashes(ucfirst($this->infos->projectname)));
        $this->path = "../tmp/".$this->projectname."_".$this->id;
        $this->bundlename = $this->projectname."Bundle";
        $this->bundlepath = "$this->path/$this->projectname/src/Main/$this->bundlename";

        $this->baseGenerator();
        foreach ($this->infos->getfiles() as $files){
            if ($this->upload($files)) {
                $this->addHtmlFile($files->getClientOriginalName());
            }
        }
        $this->addFunctions($this->checkOptionsValidity($this->infos), $this->listUpload);
        $this->configApp($this->configApp);
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
            $symfony->writeBundleFile($this->path, $this->projectname, $this->bundlename);
        }
    }

    /**
     * Upload les fichiers
     * @return string
     */
    private function upload(UploadedFile $file)
    {
        //trouver méthode plus safe que guessClientExtension()
        if ($file->guessExtension() == 'html') {
            $htmlFile = $file->getClientOriginalName();
            $file->move($this->path, $htmlFile);
            $this->listUpload[] = $htmlFile;
            return true;
        }
        elseif ($file->guessClientExtension() == 'css') {
            $cssFile = $file->getClientOriginalName();
            $file->move("$this->bundlepath/Resources/public/css/", $cssFile);
            return false;
        }
        elseif ($file->guessClientExtension() == 'js') {
            $jsFile = $file->getClientOriginalName();
            $file->move("$this->bundlepath/Resources/public/js/", $jsFile);
            return false;
        }
        elseif ($file->guessClientExtension() == 'png' || $file->guessClientExtension() == 'jpg' || $file->guessClientExtension() == 'gif' || $file->guessClientExtension() == 'jpeg') {
            $imgFile = $file->getClientOriginalName();
            $file->move("$this->bundlepath/Resources/public/images/", $imgFile);
            return false;
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

        //Déplace le fichier dans les vues
        $place = "$this->bundlepath/Resources/views/$this->projectname/$files.twig";
        rename("$this->path/$files", $place);

        if($files == 'index.html'){
            $symfony->createBaseTwigFile($this->path, $this->projectname, $place);
            $symfony->layoutCreate($this->projectname, $this->bundlepath, $place);
        }

        //Ecriture des vues
        $titre = $textParser->match_file_all("#<title>(.*)</title>#is", $place);
        $body = $textParser->match_file_all("#<body>(.*)</body>#is", $place);
        $titre = implode("",$titre[1]); $body = implode("", $body[1]);
        $textParser->replace_file("#(.*)#", "", $place);
        $textParser->filewrite($place, "{% extends 'Main$this->bundlename::layout.html.twig' %}\n{% block title %}$titre{% endblock %}\n{% block ".$this->projectname."_body %}$body{% endblock %}");

        // Enregistre la page dans la config
        $this->configApp[] = $files;

        return $place;
    }

    /**
     * Vérifie et ajoute les options dans l'array options[]
     */
    private function checkOptionsValidity($infos){
        $options = [];
        foreach (self::CHECKOPTIONS as $key => $name) {
            if($infos->$key) { $options["$name"] = true; }
        }
        return $options;
    }

    /**
     * Ajoute les bundles au dossiers selon les options
     * @param array $json
     */
    private function addFunctions(array $options, array $listUpload)
    {
        if(isset($options['form-contact']))
        {
            $formContact = new FormContact;
            $formContact->create($listUpload, $this->bundlepath, $this->bundlename, $this->projectname);
        }
    }

    /**
     * Configure l'application
     * @param array $configApp
     */
    private function configApp(array $configApp){
        $textParser = new TextParser;
        $symfony = new generateSymfony;
        $controller = ""; $routing = "";

        foreach ($configApp as $files) {
            $nameFile = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $files));
            $controller .= "\n public function ".$nameFile."Action(){ return \$this->render('Main$this->bundlename:$this->projectname:$files.twig');}";
            if($nameFile == 'index'){
                $routing .= "main_".$this->projectname."_$nameFile:\n\040\040\040\040path:     /\n\040\040\040\040defaults: { _controller: Main$this->bundlename:$this->projectname:$nameFile }\n";
            }
            else {
                $routing .= "main_$nameFile:\n\040\040\040\040path:     /$nameFile\n\040\040\040\040defaults: { _controller: Main$this->bundlename:$this->projectname:$nameFile }\n";
            }
        }

        //Création  du controller
        $symfony->controllerGenerate($this->projectname, $this->bundlepath, $this->bundlename, $controller);

        //Ecriture de la route
        $symfony->routingGenerate($this->bundlepath, $this->path, $this->projectname, $routing);

        //Création du json et readme.md
        $this->infosCreate();

        //zippage du fichier
        $symfony->compress($this->path, $this->getLink());

        //Efface le dossier pour ne laisser que le zip
        $textParser->rmAllDir($this->path);
    }

    /**
     * Créé le json et Readme.md
     * @param array $json
     */
    private function infosCreate(){
        $json['projectname'] = $this->projectname;
        //Créé le json
        $file = fopen("$this->path/config_".$this->id.".json", 'w+');
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

      /* GETTERS AND SETTERS */

    /**
     * @return string
     */
    public function getLink(){
        return "../tmp/download/$this->id.zip";
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * @param mixed $infos
     */
    public function setInfos($infos)
    {
        $this->infos = $infos;
    }

    /**
     * @return mixed
     */
    public function getProjectname()
    {
        return $this->projectname;
    }

    /**
     * @param mixed $projectname
     */
    public function setProjectname($projectname)
    {
        $this->projectname = $projectname;
    }

    /**
     * @return mixed
     */
    public function getBundlename()
    {
        return $this->bundlename;
    }

    /**
     * @param mixed $bundlename
     */
    public function setBundlename($bundlename)
    {
        $this->bundlename = $bundlename;
    }

    /**
     * @return mixed
     */
    public function getBundlepath()
    {
        return $this->bundlepath;
    }

    /**
     * @param mixed $bundlepath
     */
    public function setBundlepath($bundlepath)
    {
        $this->bundlepath = $bundlepath;
    }

    /**
     * @return array
     */
    public function getConfigApp()
    {
        return $this->configApp;
    }

    /**
     * @param array $configApp
     */
    public function setConfigApp($configApp)
    {
        $this->configApp = $configApp;
    }

}