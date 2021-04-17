<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Bundle;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Symfony\Component\Console\Command\Command;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(new Command);
    }
}