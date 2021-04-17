<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

class Bundle implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    public function getCapabilities(): array
    {
        return array(
            "Composer\\Plugin\\Capability\\CommandProvider" => "UCRM\\Composer\\Plugins\\Bundle\\CommandProvider",
        );
    }

    public function activate( Composer $composer, IOInterface $io )
    {
        $this->composer = $composer;
        $this->io = $io;

    }

    public function deactivate( Composer $composer, IOInterface $io )
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall( Composer $composer, IOInterface $io )
    {
        // TODO: Implement uninstall() method.
    }


    protected function test()
    {
        echo "TEST\n";
    }

    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            //PluginEvents::COMMAND => []
            "post-archive" => "test"
        ];

    }

}