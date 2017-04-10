<?php
namespace Main\EdBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class CreateUserCommand extends Command {

protected function configure()
{
    $this
        ->setName('easydev:install')
        ->setDescription('Install and configure the app.')
        ->setHelp('This command allows you to install and configure the app.')
    ;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
    $command = $this->getApplication()->find('cache:clear');
    $arguments = array(
        'command' => 'cache:clear',
    );

    $greetInput = new ArrayInput($arguments);
    $command->run($greetInput, $output);

    $command = $this->getApplication()->find('assets:install');
    $arguments = array(
        'command' => 'assets:install',
        '--symlink' => true
    );

    $greetInput = new ArrayInput($arguments);
    $command->run($greetInput, $output);

}

}