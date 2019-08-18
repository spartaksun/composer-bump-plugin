<?php declare(strict_types=1);

namespace Spartaksun\ComposerBumpPlugin;


use InvalidArgumentException;
use const JSON_ERROR_NONE;
use const PHP_EOL;

final class JsonPrinter {

  /**
   * @var string
   */
  private $indentation;

  /**
   * @var string
   */
  private $newLine;

  /**
   * JsonPrinter constructor.
   * @param string $indentation
   * @param string $newLine
   */
  public function __construct(string $indentation = '  ', string $newLine = PHP_EOL) {
    $this->indentation = $indentation;
    $this->newLine = $newLine;
  }

  /**
   * @param string $json
   * @return string
   * @throws BumperException
   */
  public function toJson(string $json): string {
    $indentation = $this->indentation;
    $newLine = $this->newLine;

    if ((null === json_decode($json)) && (JSON_ERROR_NONE !== json_last_error())) {
      throw new BumperException(sprintf(
        '"%s" is not valid JSON.',
        $json
      ));
    }

    if (1 !== preg_match('/^( +|\t+)$/', $indentation)) {
      throw new BumperException(sprintf(
        '"%s" is not a valid indent.',
        $indentation
      ));
    }

    if (1 !== preg_match('/^(?>\r\n|\n|\r)$/', $newLine)) {
      throw new InvalidArgumentException(sprintf(
        '"%s" is not a valid new-line character sequence.',
        $newLine
      ));
    }

    $printed = '';
    $indentLevel = 0;
    $length = strlen($json);
    $withinStringLiteral = false;
    $stringLiteral = '';
    $noEscape = true;

    for ($i = 0; $i < $length; ++$i) {
      /**
       * Grab the next character in the string.
       */
      $character = substr($json, $i, 1);

      /**
       * Are we inside a quoted string literal?
       */
      if ('"' === $character && $noEscape) {
        $withinStringLiteral = !$withinStringLiteral;
      }

      /**
       * Collect characters if we are inside a quoted string literal.
       */
      if ($withinStringLiteral) {
        $stringLiteral .= $character;
        $noEscape = '\\' === $character ? !$noEscape : true;

        continue;
      }

      /**
       * Process string literal if we are about to leave it.
       */
      if ('' !== $stringLiteral) {
        $printed .= $stringLiteral.$character;
        $stringLiteral = '';

        continue;
      }

      /**
       * Ignore whitespace outside of string literal.
       */
      if ('' === trim($character)) {
        continue;
      }

      /**
       * Ensure space after ":" character.
       */
      if (':' === $character) {
        $printed .= ': ';

        continue;
      }

      /**
       * Output a new line after "," character and and indent the next line.
       */
      if (',' === $character) {
        $printed .= $character.$newLine.str_repeat($indentation, $indentLevel);

        continue;
      }

      /**
       * Output a new line after "{" and "[" and indent the next line.
       */
      if ('{' === $character || '[' === $character) {
        ++$indentLevel;

        $printed .= $character.$newLine.str_repeat($indentation, $indentLevel);

        continue;
      }

      /**
       * Output a new line after "}" and "]" and indent the next line.
       */
      if ('}' === $character || ']' === $character) {
        --$indentLevel;

        $trimmed = rtrim($printed);
        $previousNonWhitespaceCharacter = substr($trimmed, -1);

        /**
         * Collapse empty {} and [].
         */
        if ('{' === $previousNonWhitespaceCharacter || '[' === $previousNonWhitespaceCharacter) {
          $printed = $trimmed.$character;

          continue;
        }

        $printed .= $newLine.str_repeat($indentation, $indentLevel);
      }

      $printed .= $character;
    }

    return $printed;
  }
}
