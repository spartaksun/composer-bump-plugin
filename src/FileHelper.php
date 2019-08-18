<?php declare(strict_types=1);

namespace Spartaksun\ComposerBumpPlugin;


use Exception;

final class FileHelper {

  /**
   * @var string
   */
  private $composerFilePath;

  /**
   * @var string
   */
  private $composerFilePathBackup;

  /**
   * @var array
   */
  private $composerFileContent;

  /**
   * @var bool
   */
  private $doBackup;

  /**
   * @var JsonPrinter
   */
  private $printer;

  /**
   * FileHelper constructor.
   * @param        $filePath
   * @param        $indentSize
   * @param string $newLine
   * @param bool   $doBackup
   * @throws Exception
   */
  public function __construct($filePath, $indentSize, $newLine = \PHP_EOL, $doBackup = true) {

    $this->printer = new JsonPrinter(str_repeat(' ', $indentSize), $newLine);
    $this->doBackup = $doBackup;
    $this->composerFilePath = $filePath;
    $this->composerFilePathBackup = $filePath.'-backup';

    $this->readFile();

    return $this;

  }

  /**
   * @return $this
   * @throws Exception
   */
  private function readFile() {
    if (!file_exists($this->composerFilePath)) {
      throw new Exception("File not found: ".$this->composerFilePath, 1);
    }

    $fileContent = file_get_contents($this->composerFilePath);
    $this->composerFileContent = json_decode($fileContent, true);

    if (is_null($this->composerFileContent) || !is_array($this->composerFileContent)) {
      throw new Exception("Error in decoding JSON file with description: ".$this->getJsonError(), 1);
    }

    return $this;
  }

  public function getJsonError() {
    switch (json_last_error()) {
      case JSON_ERROR_NONE:
        return ' - No errors';
        break;
      case JSON_ERROR_DEPTH:
        return ' - Maximum stack depth exceeded';
        break;
      case JSON_ERROR_STATE_MISMATCH:
        return ' - Underflow or the modes mismatch';
        break;
      case JSON_ERROR_CTRL_CHAR:
        return ' - Unexpected control character found';
        break;
      case JSON_ERROR_SYNTAX:
        return ' - Syntax error, malformed JSON';
        break;
      case JSON_ERROR_UTF8:
        return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
      default:
        return ' - Unknown error';
        break;
    }

  }

  public function getVersion() {

    if (array_key_exists('version', $this->composerFileContent)) {
      return $this->composerFileContent['version'];
    }

    return null;
  }

  public function setVersion($version) {
    $this->composerFileContent['version'] = $version;

    return $this;
  }

  /**
   * @return FileHelper
   * @throws Exception
   */
  public function save() {
    return $this->writeFile();
  }

  /**
   * @return $this
   * @throws Exception
   */
  public function writeFile() {
    if ($this->doBackup) {
      $this->createBackupFile();
    }

    $data = $this->printer->print(json_encode(
      $this->composerFileContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    ));
    file_put_contents($this->composerFilePath, $data);

    return $this;
  }

  /**
   * @return bool
   * @throws Exception
   */
  public function createBackupFile() {

    if (!copy($this->composerFilePath, $this->composerFilePathBackup)) {
      throw new Exception('Unable to make backup copy of the file: composer.json');
    }

    return true;

  }

  public function getContents() {
    return $this->composerFileContent;
  }

  /**
   * @return bool
   * @throws Exception
   */
  public function restoreBackupFile() {
    if (!copy($this->composerFilePathBackup, $this->composerFilePath)) {
      throw new Exception('Unable to restore the backup file: composer.json-backup');
    }

    return true;
  }

}
