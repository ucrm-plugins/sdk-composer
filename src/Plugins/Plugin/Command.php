<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Plugin;

use SimpleXMLElement;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class Command extends BaseCommand
{

    /**
     * Configures this plugin for use with the composer system.
     */
    protected function configure()
    {
        $this->setName( "plugin" );

        $this->addOption( "no-dev", null, InputOption::VALUE_NONE,     "Bundle without development dependencies." );
        //$this->addOption( "file",   null, InputOption::VALUE_REQUIRED, "Bundle using file name." );
        //$this->addOption( "suffix", null, InputOption::VALUE_REQUIRED, "Bundle using file suffix." );
        //$this->addOption( "dir",    null, InputOption::VALUE_REQUIRED, "Bundle file location." );

    }



    /**
     * Handles validation of the project prior to the plugin's execution.
     *
     * @param InputInterface  $input    Input from the composer system.
     * @param OutputInterface $output   Output to the composer system.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fixPhpStorm();







    }


    protected function xmlReplace( string $path)
    {
        $file = file_get_contents( $path );

        $xml = new SimpleXMLElement($file);


    }



    protected function fixPhpStorm()
    {

        $file = file_get_contents( __PROJECT_DIR__ . "/.idea/deployment.xml" );

        $xml = new SimpleXMLElement($file);

        var_dump($xml);


    }







}