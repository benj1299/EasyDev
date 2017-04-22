<?php

namespace ED\GeneratorBundle\Generator\Options;

use ED\TextParserBundle\TextParser\TextParser;
use PHPHtmlParser\Dom;

class FormContact {

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
        return $input = [
                 "form"    => $dom->find('.contact-form'),
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
    private function parsingFile(array $input, string $file, string $bundlepath, string $bundlename){
        if($input["form"]){
            $textParser = new TextParser;
            $nameFile = str_replace('.html.twig', "", basename($file)); //TODO : Changer par un meilleur pattern
            $use = "";
            $add_type = "";
            $validation = "";

            //Création du formulaire
            $form_dir = "$bundlepath/Form";
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


            foreach ($input as $value){ //TODO: Tranformer object $value en array
                foreach ($value as $add){
                    $nameAdd     = str_replace('contact-', "", $add->getAttribute('class'));
                    $type        = ($nameAdd == 'name' || $nameAdd == 'subject') ? "TextType" : ($nameAdd == 'email') ? "EmailType" : ($nameAdd == 'message') ? "TextareaType" : ($nameAdd == 'submit') ? "SubmitType" : null;
                    $class       = !empty($add->getAttribute('class')) ? "'class' => '".$add->getAttribute('class')."'" : "";
                    $placeholder = !empty($add->getAttribute('placeholder')) ? "'placeholder' => '".$add->getAttribute('class')."'," : "";
                    $id          = !empty($add->getAttribute('id')) ? "'id' => '".$add->getAttribute('id')."'," : "";
                    $require     = is_null($add->getAttribute('required')) ? "'required' => false," : "";

                    $add_type .= "->add('".$nameAdd."', '$type', [$require 'attr' => [$placeholder $id $class], 'label' => false]')\n";
                }

            }
            $type = "<?php \n
namespace Main\\$bundlename\\Form;\n
        
use Symfony\\Component\\Form\\AbstractType;\n
use Symfony\\Component\\Form\\FormBuilderInterface;\n
$use
        
class ".$nameFile."Type extends AbstractType\n
{\n
        public function buildForm(FormBuilderInterface \$builder, array \$options)\n
        {\n
            \$builder\n
                $add_type
        }\n
 }";

            $textParser->filewrite("$bundlepath/Form/".$nameFile."Type.php", $type);

            //Création de la validation
            $form_validation = "$bundlepath/Entity";
            if(!is_dir($form_validation)){
                mkdir($form_validation);
            }
            $textParser->filewrite("$bundlepath/Entity/".$nameFile."Validation.php", $validation);
        }

    }

}