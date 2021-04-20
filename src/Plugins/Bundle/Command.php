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

        chdir(__PROJECT_DIR__);

        $manifest = json_decode( file_get_contents( "src/manifest.json" ), true );

        $io = new SymfonyStyle($input, $output);
        $io->newLine();

        #region OPTIONS

        $headers = [ "Option", "Value", "Note" ];
        $rows    = [];

        $format = $this->getComposer()->getConfig()->get("archive-format");

        if( !$format || $format !== "zip" )
        {
            $rows[] = [ "archive-format", "zip", "Forced, per the UCRM Plugin requirements." ];
            $format = "zip";
        }

        $noDev = !( $input->getOption( "no-dev" ) === FALSE )
            || ( !$this->getComposer()->getPackage()->getExtra()["bundle"]["dev"] ?? FALSE );

        $rows[] = [ "no-dev", ( $noDev ? "true" : "false" ),
            "Bundled ". ( $noDev ? "without" : "with" ) . " development dependencies." ];

        $file = $input->getOption("file")
            ?? $this->getComposer()->getPackage()->getExtra()["bundle"]["file"]
            ?? __PLUGIN_NAME__;

        $rows[] = [ "file", $file, "" ];

        $suffix = $input->getOption("suffix")
            ?? $this->getComposer()->getPackage()->getExtra()["bundle"]["suffix"]
            ?? "";

        //if( strpos( $suffix, "{" ) !== false && strpos( $suffix, "}" ) !== false )
        if( preg_match('#([A-Za-z0-9._-]*)({[A-Z_]+})([A-Za-z0-9._-]*)#m', $suffix, $matches) !== false )
        {
            $suffix = $matches[1];

            switch( $matches[2] )
            {
                case "{PLUGIN_VERSION}":
                    $suffix .= $manifest["information"]["version"];
                    break;

                // TODO: Add other suffix variables, as needed!

                default;
                    $io->error( [
                        "An unsupported variable '{$matches[2]}' was supplied in the 'suffix' option!",
                        "Currently supported variables are: PLUGIN_VERSION"
                    ] );
                    exit;
            }

            $suffix .= $matches[3];
        }

        $rows[] = [ "suffix", $suffix, "" ];

        $dir = $input->getOption("dir")
            ?? $this->getComposer()->getPackage()->getExtra()["bundle"]["dir"]
            ?? __PROJECT_DIR__ . "/zip/";

        $abs = $this->pathIsAbsolute($dir) ? $dir : getcwd() . "/$dir";

        if( !realpath($abs) )
            mkdir( $dir, 0777, TRUE );

        $path = realpath( $abs );
        $name = $file . ($suffix ? "-$suffix" : "");

        $rows[] = [ "dir", $path, "" ];
        $io->table($headers, $rows);

        #endregion

        $fs = new Filesystem();

        $fs->remove("src/composer.json");
        $fs->remove("src/composer.lock");

        $fs->copy("composer.json", "src/composer.json");
        $fs->copy("composer.lock", "src/composer.lock");

        var_dump("<" . $dir);

        $vars = self::fixSubFolders( [ $dir ] );
        $dir  = $vars[0];

        var_dump(">" . $dir);

        exit;

        if( $noDev )
        {
            $io->block( "Creating 'vendor' backup...", null, "fg=green", "" );
            $fs->remove( "src/vendor_bak" );
            $fs->mirror( "src/vendor", "src/vendor_bak" );

            $io->block( "Updating production dependencies...", null, "fg=green", "" );
            echo exec( "cd src && composer update --no-interaction --no-dev --ansi" );
        }
        else
        {
            $io->block( "Updating development dependencies...", null, "fg=green", "" );
            echo exec( "cd src && composer update --no-interaction --ansi" );
        }

        $io->newLine();
        echo exec( "cd src && composer archive --file=$name --dir=$dir --format=$format --ansi" );
        $io->newLine(2);

        if( $noDev )
        {
            $io->block( "Restoring 'vendor' backup...", null, "fg=green", "" );
            $fs->remove( "src/vendor" );
            $fs->rename( "src/vendor_bak", "src/vendor" );
        }

        $io->block( "Restoring autoload class-maps...", null, "fg=green", "" );
        echo exec( "composer dump-autoload --no-interaction --ansi" );
        $io->newLine(2);

        $io->block( "Cleaning up...", null, "fg=green", "" );

        $fs->remove("src/composer.json");
        $fs->remove("src/composer.lock");

        $io->success("Plugin bundle created successfully at: '$path".DIRECTORY_SEPARATOR."$name.$format'");
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

    private static function fixSubFolders( array $vars ): array
    {
        $path = __PROJECT_DIR__ . "/src/composer.json";
        $folders = [];

        foreach( scandir( __PROJECT_DIR__ ) as $file )
            if( $file !== "." && $file !== ".." && is_dir($file) && $file !== "src" )
                $folders[] = $file;

        $contents = file_get_contents( $path );

        $contents = preg_replace( '#("(?:./)?src/?)#m', '"', $contents );


        //$contents = preg_replace( '#("archive-format" *: *)("zip")#m', '${1}"ZIP"', $contents );

        $returns = [];

        foreach( $folders as $folder )
        {
            $contents = preg_replace( '#("(?:./)?' . $folder . '/?)#m', '"../' . $folder . '/', $contents );

            foreach($vars as $var)
            {
                if( ( $rep = preg_replace( '#("(?:./)?' . $folder . '/?)#m', '"../' . $folder . '/', $var ) ) !== false )
                {
                    var_dump("FOUND!");
                    $returns[] = $rep;
                }
            }
        }

        //$contents = preg_replace( '#("archive-format" *: *)("ZIP")#m', '${1}"zip"', $contents );

        $contents = preg_replace( '#"../sdk-#m', '"../../sdk-', $contents );

        // ../sdk

        file_put_contents( $path, $contents );

        return $returns;
    }




}