<?php

namespace ED\GeneratorBundle\Generator\Options;

use ED\TextParserBundle\TextParser\TextParser;
use PHPHtmlParser\Dom;

class FormContact {

    private $form;

    public function create(array $options, string $bundlepath, string $bundlename){
        foreach ($options as $file){
            $file_dir = str_replace("..", "", $file);
            $input = $this->analyseFile(dirname(dirname(dirname(dirname(dirname(__DIR__))))).$file_dir);
            $this->parsingFile($input, $file, $bundlepath, $bundlename);
        }
    }

    /**
     * Analyse le fichier et retourne un tableau de sa composotion
     * @param string $file
     */
    private function analyseFile(string $file){
        $dom = new Dom;
        $dom->loadFromFile($file);
        $this->form = $dom->find('.contact-form');
        return $input = [
             "name"    => $dom->find('.contact-name'),
             "email"   => $dom->find('.contact-email'),
             "subject" => $dom->find('.contact-subject'),
             "message" => $dom->find('.contact-message'),
             "submit"  => $dom->find('.contact-submit')
        ];

    }

    /**
     * Modifie selon le tableau d'analyse le fichier et son environnement
     * @param array $information
     * @param string $file
     */
    private function parsingFile(array $input, string $file, string $bundlepath, string $bundlename)
    {
        if($this->form){
            $textParser = new TextParser;
            $nameFile = str_replace('.html.twig', "", basename($file)); //TODO : Changer par un meilleur pattern
            $form_dir = "$bundlepath/Form";
            $form_validation = "$bundlepath/Entity";
            $use = "";
            $add_type = "";
            $var = "";
            $setget = "";

            //Création du formulaire
            if(!is_dir($form_dir)){
                mkdir($form_dir);
            }

            //ajout des use
            if(isset($input['name']) || isset($input['subject'])){
                $use .= "use Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType;\n";
            }
            if(isset($input['email'])){
                $use .= "use Symfony\\Component\\Form\\Extension\\Core\\Type\\EmailType;\n";
            }
            if(isset($input['message'])){
                $use .= "use Symfony\\Component\\Form\\Extension\\Core\\Type\\TextareaType;\n";
            }
            if(isset($input['submit'])){
                $use .= "use Symfony\\Component\\Form\\Extension\\Core\\Type\\SubmitType;\n";
            }


            //ajout des champs
            //TODO : Finir d'ajouter les options des champs


            foreach ($input as $value){
                foreach ($value as $add){
                    $nameAdd     = str_replace('contact-', "", $add->getAttribute('class'));
                    switch ($nameAdd) {
                        case 'name': $type = "TextType"; break;
                        case 'subject': $type = "TextType"; break;
                        case 'email': $type = "EmailType"; break;
                        case 'message': $type = "TextareaType"; break;
                        case 'submit': $type = "SubmitType"; break;
                        default: $type = null;
                    }
                    $class       = !empty($add->getAttribute('class')) ? "'class' => '".$add->getAttribute('class')."'" : "";
                    $placeholder = !empty($add->getAttribute('placeholder')) ? "'placeholder' => '".$add->getAttribute('class')."'," : "";
                    $id          = !empty($add->getAttribute('id')) ? "'id' => '".$add->getAttribute('id')."'," : "";
                    $require     = is_null($add->getAttribute('required')) ? "'required' => false," : "";

                    $add_type .= "\t\t->add('".$nameAdd."', $type::class, [$require 'attr' => [$placeholder $id $class], 'label' => false])\n";
                }

            }
            $type = "<?php
namespace Main\\$bundlename\\Form;
        
use Symfony\\Component\\Form\\AbstractType;
use Symfony\\Component\\Form\\FormBuilderInterface;
$use
        
class ".$nameFile."Type extends AbstractType
{
    public function buildForm(FormBuilderInterface \$builder, array \$options)
    {
        \$builder
$add_type\t}
 }";

$textParser->filewrite("$form_dir/".$nameFile."Type.php", $type);

    //Création de la validation
    if(!is_dir($form_validation)){
        mkdir($form_validation);
    }

foreach ($input as $value){
    foreach ($value as $add){
        $nameAdd     = str_replace('contact-', "", $add->getAttribute('class'));
        $setget .= "
     /**
     * @return mixed
     */
    public function get".ucfirst($nameAdd)."()
    {
        return \$this->$nameAdd;
    }

    /**
     * @param mixed \$$nameAdd
     */
    public function set".ucfirst($nameAdd)."(\$$nameAdd)
    {
        \$this->$nameAdd = \$$nameAdd;
    }\n";
    }
}


    $validation = "<?php
namespace Main\\$bundlename\\Entity;

use Symfony\\Component\\Validator\\Constraints as Assert;

class ".$nameFile."Validation
{
$var
$setget
}";

    $textParser->filewrite("$form_validation/".$nameFile."Validation.php", $validation);
        }
    }

}