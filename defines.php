<?php /** @noinspection PhpUnused, SpellCheckingInspection */
declare(strict_types=1);

/**
 * @copyright   2019 Spaeth Technologies, Inc.
 * @author      Ryan Spaeth (rspaeth@mvqn.net)
 *
 * defines.php
 *
 * A collection of magic constants to ease plugin development.
 *
 * IMPORTANT: Many of my libraries depend on these constants, so be mindful of what you're doing when editing this file!
 *
 */

#region CONTAINER

if ( !defined( "__CONTAINER_ID__" ) )
    define( "__CONTAINER_ID__", PHP_OS !== "WINNT" ? exec( "cat /proc/1/cpuset | cut -c9-" ) : "" );

#endregion

#region DEPLOYMENT

if( !defined( "__DEPLOYMENT__" ) )
{
    class DEPLOYMENT
    {
        public const REMOTE = "REMOTE";
        public const LOCAL = "LOCAL";
    }

    define( "__DEPLOYMENT__",
        //( PHP_OS !== "WINNT" && strpos( exec( "cat /proc/1/cpuset | cut -c9-" ), exec( "echo \$HOSTNAME" ) ) === 0 )
        ( strpos( __CONTAINER_ID__, exec( "echo \$HOSTNAME" ) ) === 0 )
        ? "REMOTE"
        : "LOCAL"
    );
}

#endregion

#region PROJECT

if( !defined( "__PROJECT_DIR__" ) )
    define( "__PROJECT_DIR__", getcwd() );

if( !defined( "__PROJECT_NAME__" ) )
    define( "__PROJECT_NAME__", basename( __PROJECT_DIR__ ) );

#endregion

#region PLUGIN

if( !defined( "__PLUGIN_DIR__" ) )
{
    $path = __PROJECT_DIR__ .
        ( __DEPLOYMENT__ === DEPLOYMENT::REMOTE
            ? ""
            : ( file_exists( __PROJECT_DIR__ . "/manifest.json") ? "/" : "/src" ) );

    define( "__PLUGIN_DIR__", realpath( $path ) );
}

if( !defined( "__PLUGIN_NAME__" ) )
    define( "__PLUGIN_NAME__", file_exists( __PLUGIN_DIR__ . "/manifest.json" )
        ? json_decode( file_get_contents( __PLUGIN_DIR__ . "/manifest.json" ), TRUE )["information"]["name"]
        : __PROJECT_NAME__
    );

#endregion

