<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\MenuLinkUri.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
/**
 * This plugin modifies menu link URIs from Revout7 for Revout8
 *
 * @MigrateProcessPlugin(
 *   id = "menu_link_uri"
 * )
 */
class MenuLinkUri extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($row->getSourceProperty('menu_name') == 'menu-oitnb-characters') {
      $value = 'internal:#' . $row->getSourceProperty('link_title');
    }
    return $value;
  }
}
