<?php


namespace Spartaksun;


use Composer\Command\BaseCommand;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(new BumpCommand());
    }
}

class Command extends BaseCommand
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
