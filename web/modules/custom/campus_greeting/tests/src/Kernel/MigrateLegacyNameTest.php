<?php

namespace Drupal\Tests\campus_greeting\Kernel;

use Drupal\Core\Extension\ModuleHandler;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Override;

/**
 * @group campus_greeting
 */
class MigrateLegacyNameTest extends KernelTestBase {
  protected static $modules = ['system', 'user', 'file', 'field', 'text', 'node', 'campus_greeting'];

  #[Override]
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installEntitySchema('node');
    $this->installConfig([
      'system',
      'field',
      'file',
      'user',
      'node',
    ]);

    $this->installSchema('node', ['node_access']);

    \Drupal::moduleHandler()->loadInclude('campus_greeting', 'php', 'campus_greeting.post_update');
  }

  public function testStripsPrefix(): void {
    // crear un nodo event con title "UCR - "
    $node = Node::create([
      'type' => 'event',
      'title' => 'UCR - Campus Greeting Test Event',
    ]);
    $node->save();
    // llamar a la funcion
    // verificar que devuelve true (que el cambio se hizo)
    $this->assertTrue(_campus_greeting_strip_prefix($node));

    $this->assertSame('Campus Greeting Test Event', $node->getTitle());

    // llamar a la funcion de nuevo
    // verificar que devuelve false (no hay ningún cambio)
    $this->assertFalse(_campus_greeting_strip_prefix($node));
  }
}
