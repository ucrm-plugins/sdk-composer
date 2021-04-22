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
        $fs = new \Symfony\Component\Filesystem\Filesystem();

        #region .idea/*

        chdir( __PROJECT_DIR__ . "/.idea/" );

        foreach( scandir( getcwd() ) as $file )
        {
            if( $file === "." || $file === ".." || is_dir($file) )
                continue;

            $contents = file_get_contents($file);
            $contents = preg_replace( "/skeleton/m", __PROJECT_NAME__, $contents );
            file_put_contents( $file, $contents );

            $event->getIO()->write( "<info>Updated file: '" . getcwd() . DIRECTORY_SEPARATOR . "$file'</info>" );

            if( preg_match("/^skeleton(.*)$/", $file, $matches) === 1 && count($matches) === 2 )
            {
                $fs->rename( $file, __PROJECT_NAME__ . $matches[1] );
                $event->getIO()->write( "<info>Renamed file: '" . getcwd() . DIRECTORY_SEPARATOR . "$file'</info>" );
            }
        }

        #endregion

        #region dev/public.php

        chdir( __PROJECT_DIR__ . "/dev/" );

        $contents = file_get_contents( "public.php" );
        $contents = preg_replace( "/skeleton/m", __PROJECT_NAME__, $contents );
        file_put_contents( "public.php", $contents );

        $event->getIO()->write( "<info>Updated file: '" . getcwd() . DIRECTORY_SEPARATOR . "public.php'</info>" );

        #endregion

        #region src/manifest.json

        chdir( __PROJECT_DIR__ . "/src/" );

        $contents = file_get_contents( "manifest.json" );
        $contents = preg_replace( "/skeleton/m", __PROJECT_NAME__, $contents );
        $contents = preg_replace( "/Skeleton/m", ucfirst( __PROJECT_NAME__ ), $contents );

        file_put_contents( "manifest.json", $contents );

        $event->getIO()->write( "<info>Updated file: '" . getcwd() . DIRECTORY_SEPARATOR . "manifest.json'</info>" );

        #endregion

        #region composer.json

        chdir( __PROJECT_DIR__ );

        $contents = file_get_contents( "composer.json" );
        $contents = preg_replace( "/skeleton/m", __PROJECT_NAME__, $contents );
        file_put_contents( "composer.json", $contents );

        $event->getIO()->write( "<info>Updated file: '" . getcwd() . DIRECTORY_SEPARATOR . "composer.json'</info>" );

        #endregion

        #region .git/

        chdir( __PROJECT_DIR__ );

        echo exec("git init");

        #endregion

    }

    public function getCapabilities(): array
    {
        return array(
            CommandProviderCapability::class => Plugin\CommandProvider::class,
            //"hook": "@php -r \"$hook = $argv[1] ?? die('This is a test');\""

        );
    }


    public static function getSubscribedEvents(): array
    {

        return [
            "post-create-project-cmd" => "postCreateProjectCommand",
        ];

    }

}

