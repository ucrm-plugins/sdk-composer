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
use Symfony\Component\Filesystem\Filesystem;
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

    }

    public function deactivate( Composer $composer, IOInterface $io )
    {
        // TODO: Implement deactivate() method.
    }

    public function uninstall( Composer $composer, IOInterface $io )
    {
        // TODO: Implement uninstall() method.
    }

    private function fixSubFolders( string $path = __PROJECT_DIR__ . "/src/composer.json" )
    {
        /*
        $folders = [];

        foreach( scandir( __PROJECT_DIR__ ) as $file )
            if( $file !== "." && $file !== ".." && is_dir($file) && $file !== "src" )
                $folders[] = $file;

        $contents = file_get_contents( $path );

        $contents = preg_replace( '#("(?:./)?src/?)#m', '"', $contents );


        //$contents = preg_replace( '#("archive-format" *: *)("zip")#m', '${1}"ZIP"', $contents );

        foreach( $folders as $folder )
            $contents = preg_replace( '#("(?:./)?'.$folder.'/?)#m', '"../'.$folder.'/', $contents );

        //$contents = preg_replace( '#("archive-format" *: *)("ZIP")#m', '${1}"zip"', $contents );

        file_put_contents( $path, $contents );
        */
    }


    public function preArchiveCommand(Event $event)
    {
        /*
        $this->io->write("<info>Forcing archive format to 'zip'</info>");
        $config = $this->composer->getConfig();
        $config->merge( [ "config" => [ "archive-format" => "zip" ] ] );

        chdir(__PROJECT_DIR__);

        if( ( $manifest = file_get_contents( __PLUGIN_DIR__ . "/manifest.json" ) ) === FALSE )
            return;

        $io = $event->getIO();
        $io->write( "<info>Valid 'manifest.json' file found!</info>" );


        $test = $event->getArguments();

        var_dump($test);

        exit;

        $manifest = json_decode( $manifest, TRUE );
        $name = $manifest["information"]["name"] ?? __PROJECT_NAME__;
        $version = $manifest["information"]["version"] ?? "";
        $file = ( $name . ( $version ? "-$version" : "" ) );

        $fs = new Filesystem();

        $fs->remove("src/composer.json");
        $fs->remove("src/composer.lock");

        $fs->copy("composer.json", "src/composer.json");
        $fs->copy("composer.lock", "src/composer.lock");

        $this->fixSubFolders();

        //var_dump($event->isDevMode());
        //$this->composer->getAutoloadGenerator()->dump($config)
        echo exec("cd src && composer dump-autoload --ansi");

        //$this->composer->getAutoloadGenerator()->
        */
    }

    public function postArchiveCommand()
    {
        /*
        chdir(__PROJECT_DIR__);

        $fs = new Filesystem();

        $fs->remove("src/composer.json");
        $fs->remove("src/composer.lock");

        echo exec("composer dump-autoload --ansi");
        */
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

            //"pre-archive-cmd"   => "preArchiveCommand",
            //"post-archive-cmd"  => "postArchiveCommand",
        ];

    }

}

