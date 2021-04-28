<?php /** @noinspection PhpUnused */
declare( strict_types=1 );

namespace UCRM\Composer\Plugins;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\PluginInterface as Plugin;
use Composer\Command\BaseCommand as Command;

/**
 * @copyright 2019 Spaeth Technologies, Inc.
 * @author    Ryan Spaeth (rspaeth@mvqn.net)
 *
 * Class CommandProvider
 *
 * @package   UCRM\Composer\Plugins
 *
 */
class CommandProvider implements CommandProviderCapability
{
    /**
     * Get the {@see Command}s this {@see Plugin} provides.
     *
     * @return Command[]
     */
    public function getCommands(): array
    {
        return [
            new Commands\BundleCommand,
            new Commands\HookCommand,
        ];

    }

}