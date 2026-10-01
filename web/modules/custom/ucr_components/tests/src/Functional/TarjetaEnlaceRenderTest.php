<?php
namespace Drupal\Tests\ucr_components\Functional;

use Drupal\node\Entity\Node;
use Drupal\Tests\BrowserTestBase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[Group('campus_greeting')]
#[RunTestsInSeparateProcesses]
class TarjetaEnlaceRenderTest extends BrowserTestBase {
  protected static $modules = ['system', 'user', 'file', 'field', 'text', 'node', 'cva', 'ucr_components'];
  protected $defaultTheme = "ucr_theme";

  public function testTarjetaEnlaceRenders(): void {
    $node = Node::create([
      'type' => 'event',
      'title' => 'Tarjeta Enlace Test Event',
      'status' => 1,
    ]);
    $node->save();

    // ir a página del nodo
    $this->drupalGet($node->toUrl());

    // assertions
    // status code debe ser 200
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->elementExists('css', '.tarjeta-enlace');
    $this->assertSession()->elementTextContains('css', '.texto-encima', 'Tarjeta Enlace Test Event');
  }
}
