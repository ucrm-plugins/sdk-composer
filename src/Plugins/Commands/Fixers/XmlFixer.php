<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Commands\Fixers;

use Exception;
use SimpleXMLElement;

class XmlFixer extends Fixer
{
    /** @var SimpleXMLElement */
    protected $root;


    /**
     *
     *
     * @param string $xpath
     * @param array  $replaces
     *
     * @return $this
     * @throws Exception
     */
    public function replace( string $xpath, array $replaces ): self
    {
        if( !$this->root )
            $this->root = new SimpleXMLElement( $this->text );

        foreach( $this->root->xpath($xpath) as $element )
            foreach( $replaces as $attribute => $value )
                $element[$attribute] = $value;

        $this->text = $this->root->asXML();

        return $this;
    }

}