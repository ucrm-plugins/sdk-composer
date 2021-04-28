<?php /** @noinspection PhpUnused */
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Commands;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @copyright 2019 Spaeth Technologies, Inc.
 * @author    Ryan Spaeth (rspaeth@mvqn.net)
 *
 * Class HookCommand
 *
 * @package   UCRM\Composer\Plugins\Commands
 *
 */
class HookCommand extends BaseCommand
{
    /**
     * Configures this command for use with the composer system.
     */
    protected function configure()
    {
        $this->setName( "hook" );

        $this->addArgument( "hook", InputArgument::REQUIRED,
            "The hook to execute (install|update|configure|enable|disable|remove)" );
    }

    /**
     * Executes when this command is used.
     *
     * @param InputInterface  $input  Input from the composer system.
     * @param OutputInterface $output Output to the composer system.
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $hook  = $input->getArgument("hook");
        $file  = __PLUGIN_DIR__ . DIRECTORY_SEPARATOR . "hook_$hook.php";
        $hooks = [
            "install",
            "update",
            "configure",
            "enable",
            "disable",
            "remove"
        ];

        if( !in_array($hook, $hooks) )
            throw new RuntimeException("Hook: '$hook' is not supported by UCRM.");

        if( !file_exists( $file ) )
            throw new RuntimeException("File: '$file' could not be found.");

        $io->section("Simulating '$hook' hook...");

        /** @noinspection PhpIncludeInspection */
        include $file;

    }

}