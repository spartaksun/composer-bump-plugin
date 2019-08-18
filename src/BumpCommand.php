<?php declare(strict_types=1);

namespace Spartaksun\ComposerBumpPlugin;

use Composer\Command\BaseCommand;
use Exception as ExceptionAlias;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class BumpCommand extends BaseCommand {
  private $defaultIndent = 2;

  protected function configure(): void {
    $this->setName('bump')
         ->addArgument(
           'part',
           InputArgument::OPTIONAL,
           'Which version part to bump?'
         )
         ->addOption(
           'indent',
           'i',
           InputOption::VALUE_OPTIONAL,
           '',
           $this->defaultIndent
         )
         ->addArgument(
           'no-backup',
           InputArgument::OPTIONAL,
           '',
           false
         );
  }

  /**
   * @param InputInterface  $input
   * @param OutputInterface $output
   * @return int|void|null
   * @throws ExceptionAlias
   */
  protected function execute(InputInterface $input, OutputInterface $output): void {
    $noBackup = $input->getArgument('no-backup');
    $bumper = new Bumper((int)$input->getOption('indent'), !$noBackup);
    $scripts = $bumper->getScripts();

    if (!empty($scripts['pre-bump'])) {
      exec(sprintf($scripts['pre-bump'].' --old-version %s', $bumper->getOldVersion()), $o);
      $output->writeln(sprintf('<info>Pre bump output:</info>'));
      $this->writeOutput($o, $output);
    }

    $bumper->bump($input->getArgument('part'));

    if (!empty($scripts['post-bump'])) {
      exec(sprintf($scripts['post-bump'].' --old-version %s --new-version %s',
        $bumper->getOldVersion(),
        $bumper->getNewVersion()
      ), $o);
      $output->writeln(sprintf('<info>Post bump output:</info>'));
      $this->writeOutput($o, $output);
    }

    $output->writeln(sprintf('<info>Bumped from %s to %s</info>',
      $bumper->getOldVersion(),
      $bumper->getNewVersion()
    ));
  }

  private function writeOutput($value, OutputInterface $output): void {
    if (is_array($value)) {
      foreach ($value as $el) {
        $output->writeln($el);
      }
    } else {
      $output->writeln($value);
    }
  }
}
