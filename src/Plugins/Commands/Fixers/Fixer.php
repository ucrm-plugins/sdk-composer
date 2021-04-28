<?php /** @noinspection PhpUnused */
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Commands\Fixers;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * @copyright 2019 Spaeth Technologies, Inc.
 * @author    Ryan Spaeth (rspaeth@mvqn.net)
 *
 * Class Fixer
 *
 * @package   UCRM\Composer\Plugins\Commands\Fixers
 *
 */
abstract class Fixer
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $text;

    /**
     * @param string $path              The path to the file whose contents will be fixed.
     */
    public function __construct( string $path )
    {
        if( ( $this->path = realpath( $path ) ) === false )
            throw new FileNotFoundException();

        $this->text = file_get_contents( $this->path );
    }

    //abstract public function replace();

    /**
     * Saves the current content back to the filesystem.
     */
    public function save()
    {
        file_put_contents( $this->path, $this->text );
    }

    /**
     * Returns this {@see Fixer} as a string value, typically the raw/text contents.
     *
     * @return false|string
     */
    public function __toString()
    {
        return $this->text;
    }

}