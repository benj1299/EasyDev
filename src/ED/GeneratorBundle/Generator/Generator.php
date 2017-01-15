<?php

namespace ED\GeneratorBundle\Generator;

use ED\GeneratorBundle\Generator\Functions\FormContact;
use \ZipArchive;

class Generator extends Library
{
    protected static $id;
    protected $path;
    protected $projectname;
    protected $bundlename;
    protected $bundlepath;
    protected $fileHtml;
    protected $fileCss;
    protected $fileJs;

    public function __construct($projectname)
    {
        self::$id = bin2hex(openssl_random_pseudo_bytes(random_int(2, 5)));
        $this->projectname = strip_tags(addslashes(ucfirst($projectname)));
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
        }
    }

    public function upload($file)
    {
        if ($file->getHtmlfile()->guessExtension() == 'html') {
            $this->fileHtml = $file->getHtmlfile()->getClientOriginalName();
            $file->getHtmlfile()->move($this->path, $this->fileHtml);
            return "$this->path/$this->fileHtml";
        }
        if ($file->getCssfile()->guessExtension() == 'css') {
            $this->fileCss = $file->getCssfile()->getClientOriginalName();
            $file->getCssfile()->move($this->path, $this->fileCss);
            return "$this->path/$this->fileCss";
        }
        if ($file->getJsfile()->guessExtension() == 'js') {
            $this->fileJs = $file->getJsfile()->getClientOriginalName();
            $file->getJsfile()->move($this->path, $this->fileJs);
            return "$this->path/$this->fileJs";
        }
        // return une erreur
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
            $this->replace_file("#<body>(.*)</body>#is", "<body>\n{% block body %}\n{% javascripts '@Main$this->bundlename/Resources/public/js/*' %}<script src='{{ asset_url }}'></script>{% endjavascripts %}\n{% endblock %}\n</body>", $basefile);
        }
        //Déplace le fichier dans les vues
        rename("$this->path/$this->fileHtml", "$this->bundlepath/Resources/views/$this->projectname/$this->fileHtml.twig");

        //Adaption du controller
        $this->replace_file("#Default#", $this->projectname, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $this->replace_file("#EdBundle#", $this->bundlename, "$this->bundlepath/Controller/" . $this->projectname . "Controller.php", 1);
        $this->replace_file("#extends Controller\n{#",
            "extends Controller {\n public function ".$file."Action(){ return \$this->render('Main$this->bundlename:$this->projectname:$file'); }",
            "$this->bundlepath/Controller/" . $this->projectname . "Controller.php");

        //Ecriture de la route
        $this->filewrite("$this->bundlepath/Resources/config/routing.yml", "main_$file:\n\tpath:     /\n\tdefaults: { _controller: Main$this->bundlename:$this->projectname:$file }\n");

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
        if($json['ed_contact'] === true)
        {
            new FormContact($this->bundlepath, $this->projectname, $file);
        }
    }

    public function jsonCreate(array $json){
        $json['projectname'] = $this->projectname;
        $file = fopen("$this->path/config_".self::$id.".json", 'w+');
        fputs($file, json_encode($json));
        fclose($file);
    }
}