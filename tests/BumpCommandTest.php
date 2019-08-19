<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;
use Spartaksun\ComposerBumpPlugin\BumperException;

class BumpCommandTest extends TestCase {

  public function testCanBeCreated(): void {
    $this->assertTrue(true);
  }

  /**
   * @throws BumperException
   */
  public function testCannotBeCreated(): void {
    $this->expectException(BumperException::class);

    throw new BumperException();
  }
}
