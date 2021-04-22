<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Plugin;

use Composer\Command\BaseCommand;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class HookCommand extends BaseCommand
{
    /**
     * Configures this plugin for use with the composer system.
     */
    protected function configure()
    {
        $this->setName( "hook" );

        //$this->addOption( "name", null, InputOption::VALUE_REQUIRED, "The Plugin's name." );
        //$this->addOption( "host", null, InputOption::VALUE_REQUIRED, "The Plugin's remote host." );

        //$this->addOption( "fix-phpstorm", null, InputOption::VALUE_NONE, "Fix the .idea/* files for PhpStorm." );
        //$this->addOption( "file",   null, InputOption::VALUE_REQUIRED, "Bundle using file name." );
        //$this->addOption( "suffix", null, InputOption::VALUE_REQUIRED, "Bundle using file suffix." );
        //$this->addOption( "dir",    null, InputOption::VALUE_REQUIRED, "Bundle file location." );

    }


    /**
     * Handles validation of the project prior to the plugin's execution.
     *
     * @param InputInterface  $input  Input from the composer system.
     * @param OutputInterface $output Output to the composer system.
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }


}