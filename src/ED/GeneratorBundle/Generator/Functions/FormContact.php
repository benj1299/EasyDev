<?php

namespace ED\GeneratorBundle\Generator\Functions;

use ED\GeneratorBundle\Generator\Generator;

class FormContact extends Generator {

    public function create($json)
    {
        $json = "$this->path/$json";
        $json = json_decode($json);
            if($json['contact'])
            {
            }
    }

    public function check($file)
    {
       $html = file_get_contents($file);
       if($this->preg('ed_contact', $html))
        {
            if($this->preg('contact_name', $html)) {$this->Valid('contact_name');}
            if($this->preg('contact_email', $html)) {$this->Valid('contact_email');}
            if($this->preg('contact_subject', $html)) {$this->Valid('contact_subject');}
            if($this->preg('contact_tel', $html)) {$this->Valid('contact_tel');}
            if($this->preg('contact_message', $html)) {$this->Valid('contact_message');}
        }
        return $this->data;
    }
}