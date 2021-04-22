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
use Composer\Util\Filesystem;

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
        //var_dump("*** Created Plugin! ***");
        chdir( __PROJECT_DIR__ . "/.idea/" );

        $fs = new \Symfony\Component\Filesystem\Filesystem();

        foreach( scandir( getcwd() ) as $file )
        {
            if( $file === "." || $file === ".." )
                continue;

            $contents = file_get_contents($file);
            $contents = preg_replace("/skeleton/m", __PROJECT_NAME__, $contents);
            file_put_contents($file, $contents);

            $event->getIO()->write( "<info>Updated file: '$file'</info>" . PHP_EOL );

            if( strpos( $file, "skeleton" ) === 0 )
            {
                $fs->rename( $file, __PROJECT_NAME__ );
                $event->getIO()->write( "<info>Renamed file: '$file'</info>" . PHP_EOL );
            }
        }


        chdir( __PROJECT_DIR__ );

        echo exec("git init");

        // Needs updated:
        // - deployment.xml
        // - modules.xml
        // - php.xml
        // - workspace.xml


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

