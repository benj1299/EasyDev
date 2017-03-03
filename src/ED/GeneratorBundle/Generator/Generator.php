<?php

namespace ED\GeneratorBundle\Generator;

use ED\GeneratorBundle\Command\BundleClientCommand;
use ED\GeneratorBundle\Generator\Functions\FormContact;
use \ZipArchive;

class Generator extends Library
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
        //Création du dossier Symfony
        $zip = new ZipArchive;
        if ($zip->open('../tmp/Symfony.zip') === TRUE) {
            $zip->extractTo("$this->path/");
            $zip->close();
            rename("$this->path/Symfony", "$this->path/$this->projectname");
        } else {
            //revoyer une erreur
        }

        if (is_dir("$this->path/$this->projectname")) {
            // Génération du bundle
            $this->replace_file("#EdBundle#", $this->projectname . "Bundle", "$this->path/$this->projectname/app/AppKernel.php");
            $this->replace_file("#Ed#i", $this->projectname, "$this->path/$this->projectname/app/config/routing.yml");

            //Renome avec le nom du projet
            rename("$this->path/$this->projectname/src/Main/EdBundle", $this->bundlepath);
            rename("$this->bundlepath/Controller/DefaultController.php", "$this->bundlepath/Controller/" . $this->projectname . "Controller.php");
            rename("$this->bundlepath/Resources/views/Default", "$this->bundlepath/Resources/views/$this->projectname");

            //Ecriture du layout
            $this->filewrite("$this->bundlepath/Resources/views/layout.html.twig", "{% extends '::base.html.twig' %}\n{% block body %}\n{% block ".$this->projectname."_body %}{% endblock %}\n{% endblock %}");

            //Ecriture du fichier bundle
            rename("$this->path/$this->projectname/src/Main/$this->bundlename/MainEDBundle.php", "$this->path/$this->projectname/src/Main/$this->bundlename/Main$this->bundlename.php");
            $this->replace_file("#EdBundle#i", $this->bundlename, "$this->path/$this->projectname/src/Main/$this->bundlename/Main$this->bundlename.php");
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
        $file = strtolower(preg_replace('#\.[a-zA-Z0-9]{1,10}#', '', $this->fileHtml));
        //Génération du fichier base.html.twig
        if($file === 'index'){
            $basefile = "$this->path/$this->projectname/app/Resources/views/base.html.twig";
            copy("$this->path/$this->fileHtml", $basefile);
            //Suppression des liens CSS, JS
            $this->replace_file("#<link(.*?)>#is", '', $basefile, 1);
            $this->replace_file("#<script(.*?)>(.*)<\/script>#is", "", $basefile, 1);
            //Création des assets
            $this->replace_file("#<title>(.*)</title>#is", "<title>{% block title %}{% endblock %}</title>\n{% stylesheets 'bundles/$this->projectname/css/*' filter='cssrewrite' %}<link rel='stylesheet' href='{{ asset_url }}' />{% endstylesheets %}", $basefile);
            $this->replace_file("#<body>(.*)</body>#is", "<body>\n{% block body %}{% endblock %}\n</body>", $basefile);
        }
        //Déplace le fichier dans les vues
        rename("$this->path/$this->fileHtml", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");

        //Adaption du controller
        $this->replace_file("#Default#", $this->projectname, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $this->replace_file("#EdBundle#", $this->bundlename, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $this->replace_file("#extends Controller\n{#",
            "extends Controller {\n public function ".$file."Action(){ return \$this->render('Main$this->bundlename:$this->projectname:$this->fileHtml.twig'); }",
            "$this->bundlepath/Controller/" . $this->projectname . "Controller.php");

        //Ecriture de la route
        $this->filewrite("$this->bundlepath/Resources/config/routing.yml", "main_$file:\n\040\040\040\040path:     /\n\040\040\040\040defaults: { _controller: Main$this->bundlename:$this->projectname:$file }\n");

        //Ecriture des vues
        $titre = $this->match_file_all("#<title>(.*)</title>#is", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");
        $body = $this->match_file_all("#<body>(.*)</body>#is", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");
        $titre = implode("",$titre[1]); $body = implode("", $body[1]);
        $this->replace_file("#(.*)#", "", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");
        $this->filewrite("$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig", "{% extends 'Main$this->bundlename::layout.html.twig' %}\n{% block title %}$titre{% endblock %}\n{% block ".$this->projectname."_body %}$body{% endblock %}");

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