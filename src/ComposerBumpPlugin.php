<?php declare(strict_types=1);


namespace Spartaksun\ComposerBumpPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;

/**
 * @noinspection PhpUnused
 */
final class ComposerBumpPlugin implements PluginInterface, Capable, EventSubscriberInterface {

  /**
   * @var Composer
   */
  protected $composer;

  /**
   * @var IOInterface
   */
  protected $io;

  public static function getSubscribedEvents() {
    return [
      PluginEvents::PRE_COMMAND_RUN => 'before',
    ];
  }

  public function activate(Composer $composer, IOInterface $io) {
    $this->composer = $composer;
    $this->io = $io;
  }

  public function getCapabilities() {
    return [
      'Composer\Plugin\Capability\CommandProvider' => CommandProvider::class,
    ];
  }

  public function before($a = null) {
    // TODO process it!
  }
}
