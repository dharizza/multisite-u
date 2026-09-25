<?php
namespace Drupal\Tests\campus_greeting\Kernel;

use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Verifies the kernel test wiring works.
 */
#[Group('campus_greeting')]
#[RunTestsInSeparateProcesses]
class SmokeTest extends KernelTestBase {
  public function testWiringWorks(): void {
    $this->assertTrue(TRUE);
  }
}
