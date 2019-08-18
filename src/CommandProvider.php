<?php declare(strict_types=1);

namespace Spartaksun\ComposerBumpPlugin;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;


final class CommandProvider implements CommandProviderCapability {
  public function getCommands() {
    return [new BumpCommand()];
  }
}
