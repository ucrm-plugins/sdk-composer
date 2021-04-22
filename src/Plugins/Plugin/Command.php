<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Plugin;

use Deployment;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use UCRM\Composer\Plugins\Plugin\Fixers\XmlFixer;

class Command extends BaseCommand
{

    /**
     * Configures this plugin for use with the composer system.
     */
    protected function configure()
    {
        $this->setName( "plugin" );

        $this->addOption( "fix-phpstorm", null, InputOption::VALUE_NONE, "Fix the .idea/* files for PhpStorm." );
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
        $io = new SymfonyStyle($input, $output);

        if( __DEPLOYMENT__ === Deployment::REMOTE )
        {
            $io->error( [
                "The 'bundle' command cannot be used on a remotely deployed project."
            ] );
            exit;
        }





        if($input->getOption("fix-phpstorm"))
        {
            $this->fixPhpStorm($input, $output);
        }




    }

    /**
     *
     *
     * @param string $path
     * @param string $xpath
     * @param array  $replaces
     * @param bool   $save
     *
     * @return string|bool
     * @throws Exception
     */
    protected function xmlReplace( string $xml, string $xpath, array $replaces )
    {
        //if( !( $path = realpath($path) ) )
        //    return FALSE;

        $xml  = new SimpleXMLElement( $xml ); //file_get_contents( $path ) );

        foreach( $xml->xpath($xpath) as $element )
            foreach( $replaces as $attribute => $value )
                $element[$attribute] = $value;

        //return $save ? $xml->asXML($path) : $xml->asXML();
        return $xml->asXML();

    }


    /**
     * @throws Exception
     */
    protected function fixPhpStorm(InputInterface $input, OutputInterface $output)
    {
        $xml = new XmlFixer( __PROJECT_DIR__ . "/.idea/deployment.xml" );

        $xml->replace(
            "/project/component/serverData/paths[@name='remote']/serverdata/mappings/mapping",
            [
                "deploy" => "/test1",
                "web" => "/test2"
            ]
        );

        var_dump($xml);


        exit;

        $file = file_get_contents( __PROJECT_DIR__ . "/.idea/deployment.xml" );

        //$xml = new SimpleXMLElement($file);
        $xml = $file;


        $test = $this->xmlReplace(
            //__PROJECT_DIR__ . "/.idea/deployment.xml",
            $xml,
            "/project/component/serverData/paths[@name='remote']/serverdata/mappings/mapping",
            [
                "deploy" => "/test1",
                "web" => "/test2"
            ]
        );


        var_dump( $test );

    }







}