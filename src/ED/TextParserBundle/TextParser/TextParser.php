<?php

namespace ED\TextParserBundle\TextParser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TextParser extends Controller
{
    public $data = [];

    public function preg($name, $chaine){
        return preg_match("#name\=\'$name\'|name\=\"$name\"#i", $chaine);
    }

    public function Valid($field){
        $this->data[$field] = true;
    }

    public function replace_file($string, $replace, $file, $limit = -1){
        $text = fopen($file, 'r');
        $str = file_get_contents($file);
        $str = preg_replace($string, $replace, $str, $limit);
        fclose($text);

        $text = fopen($file, 'w+');
        fwrite($text, $str);
        fclose($text);
    }

    public function match_file_all($string, $file){
        $text = fopen($file, 'r');
        $str = file_get_contents($file);
        preg_match_all($string, $str, $out, PREG_PATTERN_ORDER);
        fclose($text);
        return $out;
    }

    public function replace_file_callback($string, $function, $file, $limit = -1){
        $text = fopen($file, 'r');
        $str = file_get_contents($file);
        $str = preg_replace_callback($string, $function, $str, $limit);
        fclose($text);
        $this->filewrite($file, $str);
    }

    public function filewrite($file, $add) {
        $text = fopen($file, 'w+');
        fwrite($text, $add);
        fclose($text);
    }

    public function check($file, $name)
    {
        $html = file_get_contents($file);
        if($this->preg($name, $html)) { return true; }
        return false;
    }

}