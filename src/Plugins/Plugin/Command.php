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
     * @param string $file
     * @param string $path
     * @param mixed  $value
     * @param bool   $save
     *
     * @return bool
     * @throws Exception
     */
    protected function xmlReplace( string $file, string $xpath, $value, bool $save = true ): bool
    {
        if( !( $file = realpath($file) ) )
            return FALSE;

        $xml  = new SimpleXMLElement( file_get_contents( $file ) );

        foreach( $xml->xpath($xpath) as $element )
            var_dump($element);




    }



    protected function fixPhpStorm(InputInterface $input, OutputInterface $output)
    {


        $file = file_get_contents( __PROJECT_DIR__ . "/.idea/deployment.xml" );

        $xml = new SimpleXMLElement($file);

        //var_dump($xml);
        $test = $xml->xpath("/project/component/serverData/paths[@name='remote']/serverdata/mappings/mapping");
        var_dump($test);

    }







}