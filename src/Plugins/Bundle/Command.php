<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Bundle;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class Command extends BaseCommand
{
    protected function configure()
    {
        $this->setName("bundle");
        //$this->addOption("optimize", "o", InputOption::VALUE_NONE, "Optimize?");
        $this->addOption("dev", null, InputOption::VALUE_NONE, "Bundle with development dependencies.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $test = $this->getComposer()->getConfig()->get("archive-format");
        var_dump($test);

        $output->writeln('Executing');
        var_dump($input->getOption("dev"));

    }
}