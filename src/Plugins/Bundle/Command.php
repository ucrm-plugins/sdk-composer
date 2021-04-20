<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Bundle;

use Deployment;
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
        $this->setName( "bundle" );

        $this->addOption( "no-dev", null, InputOption::VALUE_NONE,     "Bundle without development dependencies." );
        $this->addOption( "file",   null, InputOption::VALUE_REQUIRED, "Bundle using file name." );
        $this->addOption( "suffix", null, InputOption::VALUE_REQUIRED, "Bundle using file suffix." );
        $this->addOption( "dir",    null, InputOption::VALUE_REQUIRED, "Bundle file location." );

    }

    /**
     * Handles validation of the project prior to the plugin's execution.
     *
     * @param InputInterface  $input    Input from the composer system.
     * @param OutputInterface $output   Output to the composer system.
     *
     * @return bool                     Returns TRUE if the project appears to be valid, otherwise FALSE.
     */
    protected function validate(InputInterface $input, OutputInterface $output): bool
    {
        $io = new SymfonyStyle($input, $output);

        if( __DEPLOYMENT__ === Deployment::REMOTE )
        {
            $io->error( [
                "The 'bundle' command cannot be used on a remotely deployed project."
            ] );
            exit;
        }

        if( !file_exists( __PLUGIN_DIR__ . "/manifest.json" ) || !file_exists( __PLUGIN_DIR__ . "/main.php" ) )
        {
            $io->error( [
                "The plugin at: '".__PLUGIN_DIR__."' does not contain the required plugin files.",
                "https://github.com/Ubiquiti-App/UCRM-plugins/blob/master/docs/file-structure.md#required-files",
            ] );
            exit;
        }

        if( !file_exists( __PROJECT_DIR__ . "/src" ) || !is_dir( __PROJECT_DIR__ . "/src" ) )
        {
            $io->error( [
                "The UCRM Plugin code is expected to reside at: '" . __PROJECT_DIR__ . DIRECTORY_SEPARATOR . "src'."
            ] );
            exit;
        }

        return true;
    }

    /**
     * Handles validation of the project prior to the plugin's execution.
     *
     * @param InputInterface  $input    Input from the composer system.
     * @param OutputInterface $output   Output to the composer system.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Perform project validation.
        $this->validate($input, $output);

        $io = new SymfonyStyle($input, $output);

        chdir(__PROJECT_DIR__);

        $manifest = json_decode( file_get_contents( "src/manifest.json" ), true );

        #region OPTIONS

        $format = $this->getComposer()->getConfig()->get("archive-format");

        if( !$format || $format !== "zip" )
        {
            $io->block( "Forcing archive format to 'zip', per the UCRM Plugin requirements." );
            $format = "zip";
        }

        $noDev = !( $input->getOption( "no-dev" ) === FALSE )
            || ( !$this->getComposer()->getPackage()->getExtra()["bundle"]["dev"] ?? FALSE );

        $io->note( "Bundling " . ( $noDev ? "without" : "with" ) . " development dependencies." );

        $file = $input->getOption("file")
            ?? $this->getComposer()->getPackage()->getExtra()["bundle"]["file"]
            ?? __PLUGIN_NAME__;

        $suffix = $input->getOption("suffix")
            ?? $this->getComposer()->getPackage()->getExtra()["bundle"]["suffix"]
            ?? "";

        if( strpos( $suffix, "{" ) !== false || strpos( $suffix, "}" ) !== false )
        {
            // PLUGIN_VERSION
            switch( $suffix )
            {
                case "{PLUGIN_VERSION}":
                    $suffix = $manifest["information"]["version"];
                    break;
                // TODO: Add other suffix variables, as needed!
                default;
                    break;
            }
        }

        $dir = $input->getOption("dir")
            ?? $this->getComposer()->getPackage()->getExtra()["bundle"]["dir"]
            ?? __PROJECT_DIR__ . "/zip/";

        $abs = $this->pathIsAbsolute($dir) ? $dir : getcwd() . "/$dir";

        if( !realpath($abs) )
            mkdir( $dir, 0777, TRUE );

        $path = realpath( $abs );
        $name = $file . ($suffix ? "-$suffix": "");

        $output->writeln("<info>Archive: '$path".DIRECTORY_SEPARATOR."$name.$format'</info>");

        #endregion

        $fs = new Filesystem();

        $fs->remove("src/composer.json");
        $fs->remove("src/composer.lock");

        $fs->copy("composer.json", "src/composer.json");
        $fs->copy("composer.lock", "src/composer.lock");

        //self::delDevScripts();
        self::fixSubFolders();

        if( $noDev )
        {
            //$output->writeln( "<info>Creating 'vendor' backup...</info>" );
            //$fs->remove( "src/vendor_bak" );
            //$fs->mirror( "src/vendor", "src/vendor_bak" );

            $output->writeln( "<info>Updating production dependencies...</info>" );
            echo exec( "cd src && composer update --no-interaction --no-dev --ansi" );
        }
        else
        {
            $output->writeln( "<info>Updating development dependencies...</info>" );
            echo exec( "cd src && composer update --no-interaction --ansi" );
        }

        echo "\n";

        $output->writeln( "<info>Creating archive '$name'...</info>" );

        echo exec( "cd src && composer archive --file $name --dir $dir --format=zip --ansi" );
        echo "\n";

        if( $noDev )
        {
            //$output->writeln( "<info>Restoring 'vendor' backup...</info>" );
            //$fs->remove( "src/vendor" );
            //$fs->rename( "src/vendor_bak", "src/vendor" );

            $output->writeln( "<info>Restoring autoload class-maps...</info>" );
            echo exec( "cd src && composer dump-autoload --no-interaction --ansi" );
            echo "\n";
            //$this->_exec("composer dump-autoload --no-interaction");

            //$this->taskComposerDumpAutoload()
            //    ->noInteraction()
            //    ->run();
        }

        //echo exec("composer update --no-interaction --no-dev --ansi");

        $fs->remove("src/composer.json");
        $fs->remove("src/composer.lock");






    }



    protected function pathIsAbsolute( $path )
    {
        // Windows
        if( preg_match( '#^[a-zA-Z]:\\\\#', $path ) )
            return true;

        //if( strpos( $path, "/" ) === 0 )
        //    return true;

        return strpos( $path, "/" ) === 0;

        //if( strpos( $path, "./" ) === 0 || strpos( $path, "../" ) === 0 )
        //    return false;


    }

    private static function fixSubFolders( string $path = __PROJECT_DIR__ . "/src/composer.json" )
    {
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

        $contents = preg_replace( '#"../sdk-#m', '"../../sdk-', $contents );

        // ../sdk

        file_put_contents( $path, $contents );
    }




}