<?php /** @noinspection PhpUnused */
declare( strict_types=1 );

namespace UCRM\Composer\Plugins;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;
use Composer\Script\Event;

/**
 * @copyright 2019 Spaeth Technologies, Inc.
 * @author    Ryan Spaeth (rspaeth@mvqn.net)
 *
 * Class Bundle
 *
 * @package   UCRM\Composer\Plugins
 * @final
 */
final class Plugin implements PluginInterface, Capable, EventSubscriberInterface
{
    /** @var Composer */
    protected $composer;

    /** @var IOInterface */
    protected $io;

    /**
     *
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate( Composer $composer, IOInterface $io )
    {
        $this->composer = $composer;
        $this->io = $io;

        /** @noinspection PhpIncludeInspection */
        require_once realpath( __DIR__ . "/../../defines.php" );

    }

    /**
     *
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function deactivate( Composer $composer, IOInterface $io )
    {
        // TODO: Implement deactivate() method.
    }

    /**
     *
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function uninstall( Composer $composer, IOInterface $io )
    {
        // TODO: Implement uninstall() method.
    }

    /**
     *
     */
    public function postCreateProjectCommand(Event $event)
    {
        var_dump("*** Created Plugin! ***");
    }

    public function getCapabilities(): array
    {
        return array(
            CommandProviderCapability::class => Plugin\CommandProvider::class
        );
    }


    public static function getSubscribedEvents(): array
    {

        return [
            "post-create-project-cmd" => "postCreateProjectCommand",
        ];

    }

}
