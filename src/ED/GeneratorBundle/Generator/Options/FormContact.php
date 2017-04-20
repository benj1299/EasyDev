<?php

namespace ED\GeneratorBundle\Generator\Options;

use ED\GeneratorBundle\Generator\Generator;
use ED\TextParserBundle\TextParser\TextParser;
use PHPHtmlParser\Dom;

class FormContact extends Generator {

    public function __construct(array $options)
    {
        foreach ($options as $file){
            $input = $this->analyseFile($file);
            $this->parsingFile($input, $file);
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
    private function parsingFile(array $input, string $file){

        if(isset($input["form"])){
            $textParser = new TextParser;
            $nameFile = basename($file);

            //Création du formulaire
            mkdir("$this->bundlepath/Form/");
            $use = "";
            $add = "";
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
            //TODO : Finir les options des champs

            foreach ($input as $key => $value){
                foreach ($value as $add){

                    $nameAdd     = str_replace('contact-', "", $add->getAttribute('class'));
                    $type = ($nameAdd == 'name' || $nameAdd == 'subject') ? "TextType" : ($nameAdd == 'email') ? "EmailType" : ($nameAdd == 'message') ? "TextareaType" : ($nameAdd == 'submit') ? "SubmitType" : null;
                    $class       = isset($add->getAttribute('class')) ? "'class' => '".$add->getAttribute('class') : "";
                    $placeholder = isset($add->getAttribute('placeholder')) ? "'placeholder' => '".$add->getAttribute('class')."'," : "";
                    $id          = isset($add->getAttribute('id')) ? "'id' => ".$add->getAttribute('id')."," : "";
                    $require     = is_null($add->getAttribute('required')) ? "'required' => false," : "";

                    $add .= "->add('".$nameAdd.", $type, [$require 'attr' => [$placeholder $id $class], 'label' => false]'),\n";
                }
            }

            $type = "<?php \n
                    namespace Main\\$this->bundlename\\Form;\n
                            
                    use Symfony\\Component\\Form\\AbstractType;\n
                    use Symfony\\Component\\Form\\FormBuilderInterface;\n
                    $use
                            
                    class ".$nameFile."Type extends AbstractType\n
                    {\n
                            public function buildForm(FormBuilderInterface \$builder, array \$options)\n
                            {\n
                                \$builder\n
                                    $add
                            }\n
                     }";
            $textParser->filewrite("$this->bundlepath/Form/$nameFile.php", $type);

            //Création de la validation
            mkdir("$this->bundlepath/Entity/");
            $textParser->filewrite("$this->bundlepath/Entity/".$nameFile."Validation.php", $validation);
        }

    }

}