<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\MenuLinkTitle.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
/**
 * This plugin modifies menu link titles from Revout7 for Revout8
 *
 * @MigrateProcessPlugin(
 *   id = "menu_link_title"
 * )
 */
class MenuLinkTitle extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value == '<span class="soc">Socialism</span><span> & </span><span class="oitnb">OITNB</span><img src="/sites/all/themes/revolutionary_output/images/oitnb.jpg" alt="OITNB" title="OITNB">') {
      $value = '<span class="socialism-oitnb"><span class="soc">Socialism</span><span class="ampersand"> & </span><span class="oitnb">OITNB</span></span>';
    }
    return $value;
  }
}
