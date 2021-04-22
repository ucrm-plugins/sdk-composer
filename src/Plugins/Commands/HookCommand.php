<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnusedParameterInspection */
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Commands;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

class HookCommand extends BaseCommand
{
    /**
     * Configures this plugin for use with the composer system.
     */
    protected function configure()
    {
        $this->setName( "hook" );

        $this->addArgument( "hook", InputArgument::REQUIRED, "The hook to execute (install|update|configure|enable|disable|remove)" );
    }


    /**
     * Handles validation of the project prior to the plugin's execution.
     *
     * @param InputInterface  $input  Input from the composer system.
     * @param OutputInterface $output Output to the composer system.
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$io = new SymfonyStyle($input, $output);

        $test = $input->getArguments();

        var_dump($test);



    }









}