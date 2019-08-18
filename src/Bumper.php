<?php declare(strict_types=1);

namespace Spartaksun\ComposerBumpPlugin;

use Composer\Factory;
use Exception;
use const PHP_EOL;


final class Bumper {

  /**
   * @var int
   */
  protected $major;

  /**
   * @var int
   */
  protected $minor;

  /**
   * @var int
   */
  protected $patch;

  /**
   * @var FileHelper
   */
  private $fileHelper;

  /**
   * @var string
   */
  private $oldVersion;

  /**
   * @var string
   */
  private $newVersion;

  /**
   * Bumper constructor.
   * @param int  $indentSize
   * @param bool $doBackup
   * @throws Exception
   */
  public function __construct($indentSize = 2, $doBackup = true) {
    $this->fileHelper = new FileHelper(Factory::getComposerFile(), $indentSize, PHP_EOL, $doBackup);
    $this->oldVersion = $this->fileHelper->getVersion();
  }

  public function getScripts() {
    $contents = $this->fileHelper->getContents();
    if ($contents['scripts']) {
      return $contents['scripts'];
    }

    return [];
  }

  /**
   * @param $part
   * @throws Exception
   */
  public function bump($part) {

    switch ($part) {
      case 'patch':
        $this->newVersion = $this->bumpPatch($this->oldVersion)->getSemVer();
        break;
      case 'minor':
        $this->newVersion = $this->bumpMinor($this->oldVersion)->getSemVer();
        break;
      case 'major':
        $this->newVersion = $this->bumpMajor($this->oldVersion)->getSemVer();
        break;
      case 'restore':
        $this->fileHelper->restoreBackupFile();
        break;
      default:
        $this->newVersion = $this->bumpPatch($this->oldVersion)->getSemVer();
    }

    $this->fileHelper->setVersion($this->newVersion)->save();
  }

  public function getSemVer() {
    return implode('.', [$this->major, $this->minor, $this->patch]);
  }

  /**
   * @param $version
   * @return $this
   * @throws Exception
   */
  public function bumpPatch($version) {
    $this->parseVersion($version);

    $this->patch++;

    return $this;
  }

  /**
   * @param $version
   * @return $this|Bumper
   * @throws Exception
   */
  public function parseVersion($version) {
    if (!$version) {
      return $this->initToZero();
    }


    $splits = explode('.', $version);

    if (count($splits) != 3) {
      throw new Exception("Error parsing the version:".$version, 1);
    }

    foreach ($splits as $key => $value) {
      if (!is_numeric($value)) {
        throw new Exception("invalid string in version: ".$version, 1);
      }
    }

    list($this->major, $this->minor, $this->patch) = $splits;

    $this->major = (int)$this->major;
    $this->minor = (int)$this->minor;
    $this->patch = (int)$this->patch;

    return $this;
  }

  /**
   * @return $this
   */
  public function initToZero() {
    $this->major = 0;
    $this->minor = 0;
    $this->patch = 0;

    return $this;
  }

  /**
   * @param $version
   * @return $this
   * @throws Exception
   */
  public function bumpMinor($version) {
    $this->parseVersion($version);

    $this->minor++;
    $this->patch = 0;

    return $this;
  }

  /**
   * @param $version
   * @return $this
   * @throws Exception
   */
  public function bumpMajor($version) {
    $this->parseVersion($version);

    $this->major++;
    $this->minor = 0;
    $this->patch = 0;

    return $this;
  }

  /**
   * @return string
   */
  public function getOldVersion(): ?string {
    return $this->oldVersion;
  }

  /**
   * @return string
   */
  public function getNewVersion(): ?string {
    return $this->newVersion;
  }
}
