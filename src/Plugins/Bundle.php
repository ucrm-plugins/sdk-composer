<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;
use Composer\Script\Event;
use UCRM\Composer\Plugins\Bundle\CommandProvider;



class Bundle implements PluginInterface, Capable, EventSubscriberInterface
{
    /** @var Composer */
    protected $composer;

    /** @var IOInterface */
    protected $io;




    public function activate( Composer $composer, IOInterface $io )
    {
        $this->composer = $composer;
        $this->io = $io;

        /** @noinspection PhpIncludeInspection */
        require_once realpath( __DIR__ . "/../../defines.php" );

        /*
        $autoload = $this->composer->getPackage()->getAutoload();
        foreach( $autoload["files"] ?? [] as $file )
            /** @noinspection PhpIncludeInspection
            require_once realpath( getcwd() . "/$file" );

        // Define defaults for things we use here...

        if( !defined( "__PROJECT_DIR__" ) )
            define( "__PROJECT_DIR__", getcwd() );

        if( !defined( "__PLUGIN_DIR__" ) )
            define( "__PLUGIN_DIR__", realpath( __PROJECT_DIR__ . "/src" ) );
        */


    }

    public function deactivate( Composer $composer, IOInterface $io )
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall( Composer $composer, IOInterface $io )
    {
        // TODO: Implement uninstall() method.
    }


    public function preArchiveCommand(Event $event)
    {

        var_dump( __DEPLOYMENT__, __PROJECT_DIR__, __PLUGIN_DIR__ );


        exit;

        $this->io->write("<info>Forcing archive format to 'zip'</info>");

        $config = $this->composer->getConfig();
        $config->merge( [ "config" => [ "archive-format" => "zip" ] ] );
        var_dump($config->get("archive-format"));


        $autoload = $this->composer->getPackage()->getAutoload();
        var_dump($autoload);

        $path = getcwd();
        var_dump($path);

        exit;
    }

    public function postArchiveCommand()
    {
        //echo "POST\n";
    }

    public function getCapabilities(): array
    {
        return array(
            CommandProviderCapability::class => CommandProvider::class
        );
    }


    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [

            "pre-archive-cmd"   => "preArchiveCommand",
            "post-archive-cmd"  => "postArchiveCommand",
        ];

    }

}

