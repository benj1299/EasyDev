<?php
namespace ED\GeneratorBundle\Generator\Library;

use ED\TextParserBundle\TextParser\TextParser;
use \ZipArchive;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use PHPHtmlParser\Dom;


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

      copy("$fileHtml", $basefile);

        //Suppression des liens CSS, JS
      $textParser->replace_file("#<link(.*?)>#is", '', $basefile, 1);
      $textParser->replace_file("#<script(.*?)>(.*)<\/script>#is", "", $basefile, 1);

      //Création des assets
      $textParser->replace_file("#<title>(.*)</title>#is", "<title>{% block title %}{% endblock %}</title>\n{% stylesheets 'bundles/$projectname/css/*' filter='cssrewrite' %}<link rel='stylesheet' href='{{ asset_url }}' />{% endstylesheets %}", $basefile);
      $textParser->replace_file("#<body>(.*)</body>#is", "<body>\n{% block body %}{% endblock %}\n</body>", $basefile);
  }

    /**
     * Ecriture du layout
     * @param $projectname
     * @param $bundlepath
     */
    public function layoutCreate(string $projectname, string $bundlepath, $file){
        $textParser = new TextParser;
        $dom = new Dom;
        $dom->loadFromFile($file);

        //TODO: Gérer les liens et les navs mieux que cela
        $nav = $dom->find('.nav')[0];

        $add = "{% extends '::base.html.twig' %}\n
        {% block body %}\n
        $nav
        {% block ".$projectname."_body %}{% endblock %}\n
        {% endblock %}";
        $textParser->filewrite("$bundlepath/Resources/views/layout.html.twig", $add);
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

    /**
     * Génère les routes du projet
     * @param string $bundlepath
     * @param string $path
     * @param string $projectname
     * @param $routing
     */
    public function routingGenerate(string $bundlepath, string $path, string $projectname, $routing){
        $textParser = new TextParser;
        $textParser->filewrite("$bundlepath/Resources/config/routing.yml", $routing);
        $textParser->replace_file("#ed#i", $projectname, "$path/$projectname/app/config/routing.yml");
    }

    /**
     * Créer une archive zip d'un dossier
     * @param $source
     * @param $destination
     * @return bool
     */
    public function compress($source, $destination){
            if (!extension_loaded('zip') || !file_exists($source)) {
                return false;
            }

            $zip = new ZipArchive();
            if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
                return false;
            }

            $source = str_replace('\\', '/', realpath($source));

            if (is_dir($source) === true)
            {
                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

                foreach ($files as $file)
                {
                    $file = str_replace('\\', '/', $file);

                    // Ignore "." and ".." folders
                    if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                        continue;

                    $file = realpath($file);

                    if (is_dir($file) === true)
                    {
                        $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                    }
                    else if (is_file($file) === true)
                    {
                        $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                    }
                }
            }
            else if (is_file($source) === true)
            {
                $zip->addFromString(basename($source), file_get_contents($source));
            }

            return $zip->close();
        }
}

