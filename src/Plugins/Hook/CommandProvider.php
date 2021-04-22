<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Hook;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            new Command
        ];
    }
}