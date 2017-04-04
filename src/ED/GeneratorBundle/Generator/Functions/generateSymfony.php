<?php
namespace ED\GeneratorBundle\Generator\Functions;

use ED\TextParserBundle\TextParser\TextParser;

class generateSymfony {

    /**
     * Création du dossier symfony de base
     * @param $path
     * @param $projectname
     * @throws \Exception
     */
    public function createBase(string $path, string $projectname) {
      $zip = new \ZipArchive;
      if ($zip->open('../tmp/Symfony.zip') === TRUE) {
          $zip->extractTo("$path/");
          $zip->close();
          rename("$path/Symfony", "$path/$projectname");
      }
      else {throw new \Exception("Une erreur s'est produite dans le système");}
  }

    /**
     * Generation du bundle principale
     * @param $path
     * @param $projectname
     */
    public function bundleCreate(string $path, string $projectname) {
      $textParser = new TextParser;
      $textParser->replace_file("#EdBundle#", $projectname . "Bundle", "$path/$projectname/app/AppKernel.php");
     }

    /**
     * Renome avec le nom du projet
     * @param $path
     * @param $projectname
     * @param $bundlepath
     */
    public function projectRename(string $path, string $projectname, string $bundlepath) {
        rename("$path/$projectname/src/Main/EdBundle", $bundlepath);
        rename("$bundlepath/Controller/DefaultController.php", "$bundlepath/Controller/" . $projectname . "Controller.php");
        rename("$bundlepath/Resources/views/Default", "$bundlepath/Resources/views/$projectname");
    }

    /**
     * Ecriture du layout
     * @param $projectname
     * @param $bundlepath
     */
    public function layoutCreate(string $projectname, string $bundlepath){
        $textParser = new TextParser;
        $textParser->filewrite("$bundlepath/Resources/views/layout.html.twig", "{% extends '::base.html.twig' %}\n{% block body %}\n{% block ".$projectname."_body %}{% endblock %}\n{% endblock %}");
    }

    /**
     * Ecriture du fichier bundle
     *
     * @param $path
     * @param $projectname
     * @param $bundlename
     */
    public function writeBundleFile(string $path, string $projectname, string $bundlename) {
      $textParser = new TextParser;
      rename("$path/$projectname/src/Main/$bundlename/MainEDBundle.php", "$path/$projectname/src/Main/$bundlename/Main$bundlename.php");
      $textParser->replace_file("#EdBundle#i", $bundlename, "$path/$projectname/src/Main/$bundlename/Main$bundlename.php");
  }

    /**
     * Génération du fichier base.html.twig
     * @param $path
     * @param $projectname
     * @param $fileHtml
     */
    public function createBaseTwigFile(string $path, string $projectname, $fileHtml){
        $textParser = new TextParser;
      $basefile = "$path/$projectname/app/Resources/views/base.html.twig";
      copy("$path/$fileHtml", $basefile);
      //Suppression des liens CSS, JS
      $textParser->replace_file("#<link(.*?)>#is", '', $basefile, 1);
      $textParser->replace_file("#<script(.*?)>(.*)<\/script>#is", "", $basefile, 1);
      //Création des assets
      $textParser->replace_file("#<title>(.*)</title>#is", "<title>{% block title %}{% endblock %}</title>\n{% stylesheets 'bundles/$projectname/css/*' filter='cssrewrite' %}<link rel='stylesheet' href='{{ asset_url }}' />{% endstylesheets %}", $basefile);
      $textParser->replace_file("#<body>(.*)</body>#is", "<body>\n{% block body %}{% endblock %}\n</body>", $basefile);
  }

    /**
     * Ajoute l'Action du controller selon le fichier ajouté
     * @param string $nameFile
     * @param string $projectname
     * @param string $bundlepath
     * @param string $bundlename
     */
    public function controllerGenerate(string $projectname, string $bundlepath, string $bundlename, string $controller){
        $textParser = new TextParser;
        $textParser->filewrite("$bundlepath/Controller/".$projectname."Controller.php",
            "<?php
            \nnamespace Main\\$bundlename\\Controller;
            \nuse Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller;
            \n\tclass ".$projectname."Controller extends Controller {
            $controller
            }");
    }

    public function routingGenerate(string $bundlepath, string $path, string $projectname, $routing){
        $textParser = new TextParser;
        $textParser->filewrite("$bundlepath/Resources/config/routing.yml", $routing);
        $textParser->replace_file("#ed#i", $projectname, "$path/$projectname/app/config/routing.yml");
    }
}

