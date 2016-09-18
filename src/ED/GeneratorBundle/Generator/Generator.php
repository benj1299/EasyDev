<?php

namespace ED\GeneratorBundle\Generator;

use ED\GeneratorBundle\Generator\Functions\FormContact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ED\GeneratorBundle\Command\BundleGenerate;
use \ZipArchive;

class Generator extends Controller
{
    protected $data = [];
    protected $token;
    protected $path;
    protected $fileName;
    protected $projectname;

    public function __construct()
    {
        $this->token = bin2hex(openssl_random_pseudo_bytes(random_int(6, 12)));
        $this->path = "../tmp/$this->token";
    }

    protected function preg($name, $chaine){
        return preg_match("#name\=\'$name\'|name\=\"$name\"#i", $chaine);
    }

    protected function Valid($field){
        $this->data[$field] = true;
    }

    public function upload(UploadedFile $file)
    {
            $this->fileName = $this->token.'.'.$file->guessExtension();
            $file->move($this->path, $this->fileName);
            return "$this->path/$this->fileName";
    }

    public function jsoncreate(array $json) {
        $this->projectname = strip_tags(addslashes($json['projectname']));

        $file = fopen("$this->path/config_$this->token.json", 'w+');
        fputs($file, json_encode($json));
        fclose($file);

        $zip = new ZipArchive;
        if ($zip->open('../tmp/Symfony.zip') === TRUE) {
            $zip->extractTo("$this->path/");
            $zip->close();
            rename("$this->path/Symfony", "$this->path/$this->projectname");
        } else {
            //revoyer une erreur
        }
        if(is_dir("$this->path/$this->projectname")){
            $this->BundleGenerator();
            $contact = new FormContact();
            $contact->create("config_$this->token.json");
        }
        //retourner une erreur
    }

    protected function BundleGenerator()
    {
        // Génération du bundle
        $bundle = new BundleGenerate($this->projectname);
        $bundle->execute();
        //Génération du fichier base.html.twig et layout.html.twig
        $file = file_get_contents("$this->path/$this->fileName");
        preg_match("#<link(.*)>#",$file, $matches);
        preg_replace($matches[1],'#{% stylesheets "bundles/app/css/*" filter="cssrewrite" %}<link rel="stylesheet" href="{{ asset_url }}" />{% endstylesheets %}#',$file);
        preg_replace($matches[0], ' ', $file);
    }

}