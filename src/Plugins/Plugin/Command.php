<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedParameterInspection */
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
     * @param InputInterface  $input  Input from the composer system.
     * @param OutputInterface $output Output to the composer system.
     *
     * @throws Exception
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
     * @throws Exception
     */
    protected function fixPhpStorm(InputInterface $input, OutputInterface $output)
    {

        $remote = "ucrm.dev.mvqn.net";
        $local  = "0.0.0.0";



        $xml = new XmlFixer( __PROJECT_DIR__ . "/.idea/deployment.xml" );

        /** @noinspection SpellCheckingInspection */
        $xml->replace(
            "/project/component/serverData/paths[@name='remote']/serverdata/mappings/mapping",
            [
                "deploy" => "/" . __PLUGIN_NAME__,
                "web" => "/" . __PLUGIN_NAME__
            ]
        );
        //$xml->save();

        $xml = new XmlFixer( __PROJECT_DIR__ . "/.idea/php.xml" );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']/servers/server[@name='$local']",
            [
                //"host" => "localhost",
                //"port" => "4000"
            ]
        );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']/servers/server[@name='$remote']",
            [
                //"host" => "ucrm.dev.mvqn.net"
            ]
        );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']/servers/server[@name='$remote']" .
                "/path_mappings/mapping[@local-root='\$PROJECT_DIR\$/dev/public.php']",
            [
                "remote-root" => "/usr/src/ucrm/web/_plugins/" . __PLUGIN_NAME__ . "/public.php"
            ]
        );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']/servers/server[@name='$remote']" .
            "/path_mappings/mapping[@local-root='\$PROJECT_DIR\$/src']",
            [
                "remote-root" => "/data/ucrm/data/plugins/" . __PLUGIN_NAME__
            ]
        );
        //$xml->save();

        var_dump($xml);
        exit;





    }







}