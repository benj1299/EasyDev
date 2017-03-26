<?php
namespace ED\GeneratorBundle\Generator\Functions;

use ED\TextParserBundle\TextParser\TextParser;

class generateSymfony {

    public function createBase($path, $projectname) {
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
    public function bundleCreate($path, $projectname) {
      $textParser = new TextParser;

      $textParser->replace_file("#EdBundle#", $projectname . "Bundle", "$path/$projectname/app/AppKernel.php");
      $textParser->replace_file("#Ed#i", $projectname, "$path/$projectname/app/config/routing.yml");
     }

    /**
     * Renome avec le nom du projet
     * @param $path
     * @param $projectname
     * @param $bundlepath
     */
    public function projectRename($path, $projectname, $bundlepath) {
        rename("$path/$projectname/src/Main/EdBundle", $bundlepath);
        rename("$bundlepath/Controller/DefaultController.php", "$bundlepath/Controller/" . $projectname . "Controller.php");
        rename("$bundlepath/Resources/views/Default", "$bundlepath/Resources/views/$projectname");
    }

    /**
     * Ecriture du layout
     * @param $projectname
     * @param $bundlepath
     */
    public function layoutCreate($projectname, $bundlepath){
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
    public function writeBundleFile($path, $projectname, $bundlename) {
      $textParser = new TextParser;
      rename("$path/$projectname/src/Main/$bundlename/MainEDBundle.php", "$path/$projectname/src/Main/$bundlename/Main$bundlename.php");
      $textParser->replace_file("#EdBundle#i", $bundlename, "$path/$projectname/src/Main/$bundlename/Main$bundlename.php");
  }

}
