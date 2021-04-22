<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Plugin\Fixers;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

abstract class Fixer
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $text;


    public function __construct( string $path )
    {
        if( ( $this->path = realpath( $path ) ) === false )
            throw new FileNotFoundException();

        $this->text = file_get_contents( $this->path );
    }

    //abstract public function replace();

    public function save()
    {
        file_put_contents( $this->path, $this->text );
    }


    public function __toString()
    {
        return $this->text;
    }

}