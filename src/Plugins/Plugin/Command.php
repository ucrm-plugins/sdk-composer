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

class Command extends BaseCommand
{
    private const REGEX_PORT = "#^([0-9]{1,4}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])$#";
    private const REGEX_NAME = "#^([a-z0-9-]+)#";

    protected $srcRepo;
    protected $srcName;

    protected $devHost;

    protected $ideHost;
    protected $idePort;

    protected $sshHost;
    protected $sshPort;
    protected $sshUser;


    /**
     * Configures this plugin for use with the composer system.
     */
    protected function configure()
    {
        $this->setName( "plugin" );

        $this->addOption( "name", null, InputOption::VALUE_REQUIRED, "The Plugin's name." );
        $this->addOption( "host", null, InputOption::VALUE_REQUIRED, "The Plugin's remote host." );

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

        $regex = self::REGEX_NAME;

        $this->srcRepo = $io->ask("Organization:", "ucrm-plugins", [ get_class($this), "validateName"]);
        $this->srcName = $io->ask("Plugin Name :", "skeleton",     self::REGEX_NAME);

        $this->devHost = $io->ask("Remote Host :", "ucrm.dev.mvqn.net");

        $this->ideHost = $io->ask("Local Host  :", "localhost");
        $this->idePort = $io->ask("Local Port  :", "4000", self::REGEX_PORT);

        $this->sshHost = $io->ask("SSH Host    :", $this->devHost);
        $this->sshPort = $io->ask("SSH Port    :", "9022", self::REGEX_PORT);
        $this->sshUser = $io->ask("SSH User    :", "nginx");


        exit;


        // Fix composer.json

        // Fix manifest.json

        $this->fixManifest($input, $output);

        if($input->getOption("fix-phpstorm"))
        {
            $this->fixPhpStorm($input, $output);
        }




    }


    protected static function validateName(string $answer)
    {
        return ( preg_match(self::REGEX_NAME, $answer) !== FALSE) ? $answer : FALSE;
    }


    protected function fixManifest(InputInterface $input, OutputInterface $output)
    {





    }



    /**
     * @throws Exception
     */
    protected function fixPhpStorm(InputInterface $input, OutputInterface $output)
    {

        $srcName = __PLUGIN_NAME__;

        $devHost = "ucrm.dev.mvqn.net";

        $ideHost = "localhost";
        $idePort = "4000";

        $sshHost = $devHost;
        $sshPort = 9022;
        $sshUser = "nginx";







        #region .idea/deployment.xml

        $xml = new Fixers\XmlFixer( __PROJECT_DIR__ . "/.idea/deployment.xml" );

        $xml->replace(
            "/project/component[@name='PublishConfigData']",
            [
                "serverName" => "/$devHost",
            ]
        );

        /** @noinspection SpellCheckingInspection */
        $xml->replace(
            "/project/component[@name='PublishConfigData']"
            . "/serverData/paths[@name='$devHost']/serverdata/mappings/mapping",
            [
                "deploy" => "/$srcName",
                "web" => "/$srcName",
            ]
        );

        //$xml->save();

        #endregion

        #region .idea/php.xml

        $xml = new Fixers\XmlFixer( __PROJECT_DIR__ . "/.idea/php.xml" );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']"
            . "/servers/server[@name='$ideHost']",
            [
                "name" => "$ideHost",
                "host" => "$ideHost",
                "port" => "$idePort",
            ]
        );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']"
            . "/servers/server[@name='$devHost']",
            [
                "name" => "$devHost",
                "host" => "$devHost",
            ]
        );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']"
            . "/servers/server[@name='$devHost']/path_mappings/mapping[@local-root='\$PROJECT_DIR\$/dev/public.php']",
            [
                "remote-root" => "/usr/src/ucrm/web/_plugins/$srcName/public.php",
            ]
        );

        $xml->replace(
            "/project/component[@name='PhpProjectServersManager']"
            . "/servers/server[@name='$devHost']/path_mappings/mapping[@local-root='\$PROJECT_DIR\$/src']",
            [
                "remote-root" => "/data/ucrm/data/plugins/$srcName",
            ]
        );

        //$xml->save();

        #endregion

        #region .idea/sshConfigs.xml

        $xml = new Fixers\XmlFixer( __PROJECT_DIR__ . "/.idea/sshConfigs.xml" );

        $xml->replace(
            "/project/component[@name='SshConfigs']"
            . "/configs/sshConfig",
            [
                "host" => "$sshHost",
                "port" => "$sshPort",
            ]
        );

        //$xml->save();

        #endregion

        #region .idea/webServers.xml

        $xml = new Fixers\XmlFixer( __PROJECT_DIR__ . "/.idea/webServers.xml" );

        /** @noinspection HttpUrlsUsage */
        $xml->replace(
            "/project/component[@name='WebServers']/option[@name='servers']/webServer",
            [
                "name" => "$devHost",
                "url" => "http://$devHost/crm/_plugins",
            ]
        );

        $xml->replace(
            "/project/component[@name='WebServers']/option[@name='servers']/webServer[@name='$devHost']" .
                "/fileTransfer[@rootFolder='/data/ucrm/data/plugins']",
            [
                "host" => "$sshHost",
                "port" => "$sshPort",
                "sshConfig" => "nginx@$sshHost:$sshPort password"
            ]
        );

        //$xml->save();

        #endregion





        var_dump($xml);
        exit;





    }







}