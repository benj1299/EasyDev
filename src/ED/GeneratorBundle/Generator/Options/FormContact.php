<?php

namespace ED\GeneratorBundle\Generator\Options;

use ED\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;

class FormContact extends Generator  {

    protected $file;
    protected $fileName;
    protected $bundlepath;
    protected $data;

    public function __construct($bundlepath ,$fileName)
    {
        parent::__construct($infos);

        $this->bundlepath = $bundlepath;
        $this->fileName = $fileName;
        $this->file = "$this->bundlepath/Resources/views/".$this->infos->projectname."/$fileName.html.twig";

        //Créé le dossier Form
        $fs = new Filesystem();
        $fs->mkdir("$this->bundlepath/Form/", 0777);

        //Variable de contenu
        $use = "use Symfony\Component\Form\AbstractType;\nuse Symfony\Component\Form\FormBuilderInterface;";

        $content = '<?php
namespace Main\\'.$this->bundlename.'\Form;

'.$use.'

class '.ucfirst($this->fileName).'Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder';
/*
        //Détection des composants du form
        $out = $this->match_file_all("#<input .*type=('|\")(.*)('|\").*>#", $this->file);
        $input = $out[2];

        foreach ($input as $name){
            $type = ["text" => "TextType::class", "password" => ""];
            foreach ($type as $pattern => $class){
                if(preg_match("#^$pattern#", $name))
                {
                    $use .= "\n use Symfony\Component\Form\Extension\Core\Type\\$class;"
                    $content .= "\n->add('$name', $class)"
                }
            }

        }
*/
        //Génère le fichier
        $this->filewrite("$this->bundlepath/Form/".ucfirst($this->fileName)."Form.php", $content);
        $this->FormGenerator();
    }

    //Génère la vue
    private function FormGenerator()
    {
        //Balise à remplace dans la vue
        $add = ["<form (.*)>" => "{{ form_start(form) }}", '</form>' => "{{ form_rest(form) }}\n{{ form_end(form) }}",
            "<input .*name=('|\")contact_name('|\").*>" => "{{ form_widget(form.contact_name) }}",
            "<input .*name=('|\")contact_email('|\").*>" => "{{ form_widget(form.contact_email) }}",
            "<input .*name=('|\")contact_subject('|\").*>" => "{{ form_widget(form.contact_subject) }}",
            "<input .*name=('|\")contact_tel('|\").*>" => "{{ form_widget(form.contact_tel) }}",
            "<textarea .*name=('|\")contact_message('|\").*>(.*)</textarea>" => "{{ form_widget(form.contact_message) }}"];

        foreach ($add as $pattern => $replace)
        {
            $this->replace_file("#$pattern#", $replace, $this->file);
            $this->data[] = $replace;
        }
    }

    //Génère la validation du form
    private function ValidationGenerator()
    {

    }

    //Génère la fonction d'envoie d'email
    private function ContactFunction()
    {

    }

}