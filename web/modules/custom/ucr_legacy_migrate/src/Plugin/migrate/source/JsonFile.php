<?php

namespace Drupal\ucr_legacy_migrate\Plugin\migrate\source;

use Drupal\migrate\Attribute\MigrateSource;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;

/**
 * Lee filas desde un archivo JSON dentro de un módulo.
 *
 * Ejemplo en la migración:
 * @code
 * source:
 *   plugin: ucr_json_file
 *   module: ucr_legacy_migrate
 *   path: data/noticias.json
 *   ids:
 *     id:
 *       type: integer
 * @endcode
 *
 * El archivo debe ser una lista de objetos, por ejemplo:
 * [{"id": 1, "titulo": "..."}, {"id": 2, "titulo": "..."}]
 *
 * En proyectos reales, para JSON, CSV o XML se suele usar el módulo contrib
 * migrate_plus (plugin "url") o migrate_source_csv. Este plugin existe para
 * que el ejercicio funcione solo con core.
 */
#[MigrateSource('ucr_json_file')]
class JsonFile extends SourcePluginBase {

  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    foreach (['module', 'path', 'ids'] as $key) {
      if (empty($configuration[$key])) {
        throw new MigrateException("Falta la opción '$key' en el source ucr_json_file.");
      }
    }
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
  }

  /**
   * Ruta absoluta del archivo JSON.
   */
  protected function getFilePath(): string {
    $module_path = \Drupal::service('extension.list.module')->getPath($this->configuration['module']);
    return DRUPAL_ROOT . '/' . $module_path . '/' . $this->configuration['path'];
  }

  /**
   * Lee y decodifica el archivo.
   */
  protected function readRows(): array {
    $file = $this->getFilePath();
    if (!is_readable($file)) {
      throw new MigrateException("No se puede leer el archivo $file.");
    }
    try {
      $rows = json_decode(file_get_contents($file), TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException $e) {
      throw new MigrateException("El archivo $file no es JSON válido: " . $e->getMessage());
    }
    if (!is_array($rows) || !array_is_list($rows)) {
      throw new MigrateException("El archivo $file debe contener una lista de objetos.");
    }
    return $rows;
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeIterator(): \Iterator {
    return new \ArrayIterator($this->readRows());
  }

  /**
   * {@inheritdoc}
   */
  public function fields(): array {
    $rows = $this->readRows();
    $first = $rows[0] ?? [];
    $names = array_keys($first);
    return array_combine($names, $names);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds(): array {
    return $this->configuration['ids'];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString(): string {
    return $this->configuration['module'] . ':' . $this->configuration['path'];
  }

}
