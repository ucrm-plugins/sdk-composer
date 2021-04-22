<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Bundle;

use Deployment;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Project
{

    /**
     * Handles validation of the project prior to the plugin's execution.
     *
     * @param InputInterface  $input    Input from the composer system.
     * @param OutputInterface $output   Output to the composer system.
     *
     * @return bool                     Returns TRUE if the project appears to be valid, otherwise FALSE.
     */
    public static function validate(InputInterface $input, OutputInterface $output): bool
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
                "See: https://github.com/Ubiquiti-App/UCRM-plugins/blob/master/docs/file-structure.md#required-files",
            ] );
            exit;
        }

        if( !file_exists( __PROJECT_DIR__ . "/src" ) || !is_dir( __PROJECT_DIR__ . "/src" ) )
        {
            $io->error( [
                "The UCRM Plugin code is expected to reside at: '" . __PROJECT_DIR__ . DIRECTORY_SEPARATOR . "src'.",
                "See: https://gitlab.com/ucrm-plugins/skeleton"
            ] );
            exit;
        }

        return true;
    }


    /**
     * Determines whether or not a path is absolute or relative.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolutePath( string $path ): bool
    {
        return ( preg_match( '#^[a-zA-Z]:\\\\#', $path ) || strpos( $path, "/" ) === 0 );

    }

    /**
     * Alters relative paths to coincide with the folder structure AFTER the 'bundle' command.
     *
     * @param string $path              The path to check and/or modify.
     *
     * @return string                   Returns the unmodified absolute path or the modified relative path.
     */
    public static function fixRelativeDir( string $path ): string
    {
        if( self::isAbsolutePath($path) )
            return $path;

        // The 'src' folder is unique.
        if( ( $fixed = preg_replace( '#((?:./)?src/?)#m', '', $path ) ) !== $path )
        {
            //$path = $fixed;
            //return TRUE;
            return $fixed;
        }

        if( ( $fixed = preg_replace( '#((?:./)?([A-Za-z0-9._-]+)/?)#m', '../${2}/', $path ) ) !== $path )
        {
            //$path = $fixed;
            //return TRUE;
            return $fixed;
        }

        //return FALSE;
        return $path;

        //return preg_replace( '#((?:./)?([A-Za-z0-9._-]+)/?)#m', '../${2}/', $path );




    }




    public static function fixSubFolders( string $path = __PROJECT_DIR__ . "/src/composer.json" )
    {
        $folders = [];

        foreach( scandir( __PROJECT_DIR__ ) as $file )
            if( $file !== "." && $file !== ".." && is_dir($file) && $file !== "src" )
                $folders[] = $file;

        $contents = file_get_contents( $path );

        $contents = preg_replace( '#("(?:./)?src/?)#m', '"', $contents );


        //$contents = preg_replace( '#("archive-format" *: *)("zip")#m', '${1}"ZIP"', $contents );

        //$returns = [];

        foreach( $folders as $folder )
        {
            $contents = preg_replace( '#("(?:./)?' . $folder . '/?)#m', '"../' . $folder . '/', $contents );

            /*
            foreach($vars as $var)
            {


                if( ( $rep = preg_replace( '#("(?:./)?' . $folder . '/?)#m', '"../' . $folder . '/', $var ) ) !== false )
                {
                    var_dump($rep);
                    $returns[] = $rep;
                }
            }
            */
        }

        //$contents = preg_replace( '#("archive-format" *: *)("ZIP")#m', '${1}"zip"', $contents );

        $contents = preg_replace( '#"../sdk-#m', '"../../sdk-', $contents );

        // ../sdk

        file_put_contents( $path, $contents );

        //return $returns;
    }

    public static function fixSubFolder( string $folder ): string
    {
        $folders = [];

        foreach( scandir( __PROJECT_DIR__ ) as $file )
            if( $file !== "." && $file !== ".." && is_dir($file) && $file !== "src" )
                $folders[] = $file;

        $contents = $folder;

        $contents = preg_replace( '#((?:./)?src/?)#m', '', $contents );


        //$contents = preg_replace( '#("archive-format" *: *)("zip")#m', '${1}"ZIP"', $contents );

        //$returns = [];

        foreach( $folders as $folder )
        {
            $contents = preg_replace( '#((?:./)?' . $folder . '/?)#m', '../' . $folder . '/', $contents );

            /*
            foreach($vars as $var)
            {


                if( ( $rep = preg_replace( '#("(?:./)?' . $folder . '/?)#m', '"../' . $folder . '/', $var ) ) !== false )
                {
                    var_dump($rep);
                    $returns[] = $rep;
                }
            }
            */
        }

        //$contents = preg_replace( '#("archive-format" *: *)("ZIP")#m', '${1}"zip"', $contents );

        $contents = preg_replace( '#"../sdk-#m', '"../../sdk-', $contents );

        // ../sdk

        //file_put_contents( $path, $contents );
        return $contents;
        //return $returns;
    }








}