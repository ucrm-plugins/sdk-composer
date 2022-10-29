<?php

declare(strict_types=1);

namespace UCRM\SDK\Composer\Plugins;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Composer\Plugin\PluginInterface as Plugin;
use Composer\Command\BaseCommand as Command;
use UCRM\SDK\Composer\Plugins\Commands\BundleCommand;
use UCRM\SDK\Composer\Plugins\Commands\HookCommand;

/**
 * @copyright 2019 Spaeth Technologies Inc.
 * @author    Ryan Spaeth (rspaeth@spaethtech.com)
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
        $commands = [];

        $cwd = getcwd();
        echo ">>> $cwd\n";

        if (file_exists("$cwd/manifest.json"))
            $commands[] = new BundleCommand;

        $hooks = glob("$cwd/hook_*.php");

        var_dump($hooks);

        if ($hooks || count($hooks) > 0) {
            $commands[] = new HookCommand;
        }

        //if (file_exists("$cwd/manifest.json"))
        //    $commands[] = new Commands\BundleCommand();

        return $commands;
        // return [
        //     new Commands\BundleCommand,
        //     new Commands\HookCommand,
        // ];
    }
}
