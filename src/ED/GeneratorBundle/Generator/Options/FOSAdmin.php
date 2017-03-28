<?php

namespace ED\GeneratorBundle\Generator\Options;

use ED\GeneratorBundle\Generator\Generator;

class FOSAdmin extends Generator  {

    protected $file;
    protected $fileName;
    protected $bundlepath;
    protected $data;

    public function __construct($bundlepath ,$fileName)
    {
        parent::__construct($infos);
    }

}