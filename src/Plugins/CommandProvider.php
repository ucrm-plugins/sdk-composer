<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

/**
 * @copyright 2019 Spaeth Technologies, Inc.
 * @author    Ryan Spaeth (rspaeth@mvqn.net)
 *
 * Class CommandProvider
 *
 * @package   UCRM\Composer\Plugins\Commands
 *
 */
class CommandProvider implements CommandProviderCapability
{
    public function getCommands(): array
    {
        return [
            new Commands\BundleCommand,
            new Commands\PluginCommand,
            new Commands\HookCommand
        ];
    }
}