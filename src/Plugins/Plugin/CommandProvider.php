<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Plugin;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use UCRM\Composer\Plugins\Hook\HookCommand;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            new Command,
            new HookCommand
        ];
    }
}