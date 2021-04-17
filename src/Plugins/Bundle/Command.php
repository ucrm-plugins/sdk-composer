<?php
declare( strict_types=1 );

namespace UCRM\Composer\Plugins\Bundle;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class Command extends BaseCommand
{
    protected function configure()
    {
        $this->setName("bundle");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Executing');
    }
}