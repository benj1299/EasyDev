<?php

namespace ED\GeneratorBundle\Generator;

use ED\GeneratorBundle\Command\BundleClientCommand;
use ED\GeneratorBundle\Generator\Options\FormContact;
use ED\TextParserBundle\TextParser\TextParser;
use ED\GeneratorBundle\Generator\Functions\generateSymfony;

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

    public function __construct($infos)
    {
        self::$id = bin2hex(openssl_random_pseudo_bytes(random_int(2, 5)));
        $this->infos = $infos;
        $this->projectname = strip_tags(addslashes(ucfirst($this->infos->projectname)));
        $this->path = "../tmp/".$this->projectname."_".self::$id;
        $this->bundlename = $this->projectname."Bundle";
        $this->bundlepath = "$this->path/$this->projectname/src/Main/$this->bundlename";
    }

    public function baseGenerator(){
            $symfony = new generateSymfony;
            $symfony->createBase($this->path, $this->projectname);

        if (is_dir("$this->path/$this->projectname")) {
            $symfony->bundleCreate($this->path, $this->projectname);
            $symfony->projectRename($this->path, $this->projectname, $this->bundlepath);
            $symfony->layoutCreate($this->projectname, $this->bundlepath);
            $symfony->writeBundleFile($this->path, $this->projectname, $this->bundlepath, $this->bundlename);
        }
    }

    public function upload()
    {
        if ($this->infos->getfiles()->guessExtension() == 'html') {
            $this->fileHtml = $this->infos->getfiles()->getClientOriginalName();
            $this->infos->getfiles()->move($this->path, $this->fileHtml);
            return "$this->path/$this->fileHtml";
        }
        elseif ($this->infos->getfiles()->guessExtension() == 'css') {
            $this->fileCss = $this->infos->getfiles()->getClientOriginalName();
            $this->infos->getfiles()->move($this->path, $this->fileCss);
            return "$this->path/$this->fileCss";
        }
        elseif ($this->infos->getfiles()->guessExtension() == 'js') {
            $this->fileJs = $this->infos->getfiles()->getClientOriginalName();
            $this->infos->getfiles()->move($this->path, $this->fileJs);
            return "$this->path/$this->fileJs";
        }
        else {
            // return une erreur
        }
    }

    public function addHtmlFile() {
        $textParser = new TextParser;
        $file = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $this->fileHtml));
        //Génération du fichier base.html.twig
        if($file === 'index'){
            $basefile = "$this->path/$this->projectname/app/Resources/views/base.html.twig";
            copy("$this->path/$this->fileHtml", $basefile);
            //Suppression des liens CSS, JS
            $textParser->replace_file("#<link(.*?)>#is", '', $basefile, 1);
            $textParser->replace_file("#<script(.*?)>(.*)<\/script>#is", "", $basefile, 1);
            //Création des assets
            $textParser->replace_file("#<title>(.*)</title>#is", "<title>{% block title %}{% endblock %}</title>\n{% stylesheets 'bundles/$this->projectname/css/*' filter='cssrewrite' %}<link rel='stylesheet' href='{{ asset_url }}' />{% endstylesheets %}", $basefile);
            $textParser->replace_file("#<body>(.*)</body>#is", "<body>\n{% block body %}{% endblock %}\n</body>", $basefile);
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

    public function addAssetFile() {
     if($this->fileCss){rename("$this->path/$this->fileCss", "$this->bundlepath/Resources/public/$this->fileCss");}
     if($this->fileJs){rename("$this->path/$this->fileJs", "$this->bundlepath/Resources/public/$this->fileJs");}
    }

    public function addFunctions(array $json)
    {
        $file = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $this->fileHtml));
        if(!empty($json['ed_contact']) && $json['ed_contact'] === true)
        {
            new FormContact($this->bundlepath, $file);
        }
        if(!empty($json['ed_fos_admin']) && $json['ed_fos_admin'] === true)
        {
            new FOSAdmin($this->bundlepath, $file);
        }
    }

    public function InfosCreate(array $json){
        $json['projectname'] = $this->projectname;
        //Créé le json
        $file = fopen("$this->path/config_".self::$id.".json", 'w+');
        fputs($file, json_encode($json));
        fclose($file);
        //Créé le TODO
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