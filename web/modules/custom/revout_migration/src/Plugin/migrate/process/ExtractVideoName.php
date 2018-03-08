<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\ExtractVideoName.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Component\Utility\Html;

/**
 * This plugin extracts inner text from anchors and uses as the Name field
 * of Video entities.
 *
 * @MigrateProcessPlugin(
 *   id = "extract_video_name"
 * )
 */
class ExtractVideoName extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $dom = HTML::load($value);
    $anchors = $dom->getElementsByTagName('a');
    $text = '';

    /**
     * There will only be one anchor, but calling the above DOM method and
     * "looping" through a one result Object is the easiest way.
     */
    foreach ($anchors as $anchor) {
      /**
       * All video anchors in the node body from Revout7 have
       * class="colorbox-load"; So if we find that attribute, this is a video.
       * Otherwise, $text will return empty, and this row will be skipped and
       * a Video entity won't be created.
       */
      if ($anchor->hasAttribute('class')) {
        $class = $anchor->getAttribute('class');
        if (strpos($class, 'colorbox-load') !== FALSE) {
          $text = $anchor->textContent;
        }
      }
    }
    return $text;
  }
}
