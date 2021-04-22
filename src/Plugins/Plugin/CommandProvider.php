<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Plugin;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            "plugin" => new Command,
            "hook" => new \UCRM\Composer\Plugins\Hook\HookCommand
        ];
    }
}