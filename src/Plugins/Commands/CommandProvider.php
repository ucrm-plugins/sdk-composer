<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Commands;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use UCRM\Composer\Plugins\Commands\HookCommand;
use UCRM\Composer\Plugins\Commands\PluginCommand;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            new PluginCommand,
            new HookCommand
        ];
    }
}