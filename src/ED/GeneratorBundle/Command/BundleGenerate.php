<?php
namespace ED\GeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\Response;

class BundleGenerate extends Controller
{
    private $projectname;

    public function __construct($projectname)
    {
        $this->projectname = $projectname;
    }

    public function execute()
    {
        $bundlename = $this->projectname."Bundle";
        $namespace = "Ed/$bundlename";

        $kernel = $this->container->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command'        => 'generate:bundle',
            '--namespace'    => $namespace,
            '--format'       => 'yml',
            '--bundle-name'  => $bundlename,
            '--share'  => true
        ));
        $application->run($input, NullOutput());
        return new Response("");
    }

}