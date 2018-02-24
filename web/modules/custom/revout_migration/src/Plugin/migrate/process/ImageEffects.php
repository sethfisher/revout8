<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\ImageEffects.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
/**
 * This plugin maps Image Effect values from Revout7 to Revout8
 *
 * @MigrateProcessPlugin(
 *   id = "image_effects"
 * )
 */
class ImageEffects extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Map Revout7 'canvas_file2canvas' Image Effect to Revout8
    // 'image_effects_watermark' Image Effect
    $value['id'] = ($value['name'] == 'canvasactions_file2canvas' ? 'image_effects_watermark' : $value['name']);

    // Map Revout7 Image Effect 'path' to Revout8 Image Effect watermark_image
    if ($value['data']['path']) {
      $value['data']['watermark_image'] = $value['data']['path'];
    }
    return $value;
  }
}
