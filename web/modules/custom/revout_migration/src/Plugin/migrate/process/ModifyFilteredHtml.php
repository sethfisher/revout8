<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\ModifyFilteredHtml.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
/**
 * This plugin modifies the Filtered HTML text format from Revout7 for Revout8
 *
 * @MigrateProcessPlugin(
 *   id = "modify_filtered_html"
 * )
 */
class ModifyFilteredHtml extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value['allowed_html'] = str_replace('<a ', '<a class target data-dialog-type ', $value['allowed_html']);
    return $value;
  }
}
