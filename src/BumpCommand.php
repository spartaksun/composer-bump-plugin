<?php


namespace Spartaksun;


use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BumpCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('bump');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Executing bump');
    }
}
