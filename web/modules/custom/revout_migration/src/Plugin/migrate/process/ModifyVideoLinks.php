<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\ModifyVideoLinks.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\media\Entity\Media;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity;

/**
 * This plugin converts video links in node body to video entity embed link
 * objects.
 *
 * @MigrateProcessPlugin(
 *   id = "modify_video_links"
 * )
 */
class ModifyVideoLinks extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $dom = HTML::load($value['value']);
    // Get all the anchors in the node body
    $anchors = $dom->getElementsByTagName('a');
    // We'll need to remove the anchors after this loop.
    $anchorsToRemove = [];
    foreach ($anchors as $anchor) {
       // All video links were marked with class="colorbox-load" in Revout7.
       // So we'll skip any links that don't have that class.
      if ($anchor->hasAttribute('class')) {
        $class = $anchor->getAttribute('class');
        if (strpos($class, 'colorbox-load') !== FALSE) {
          // Extract anchor html as string.
          $anchor_html = $dom->saveHTML($anchor);

          /**
           * The entire anchor is stored as sourceid1 in the video migration
           * map table. So we'll query the map table for the current anchor,
           * then use the returned destid1 field, which is the id of the Video
           * Media Entity the migration created in this Revout8 site.
           */
          $database = \Drupal::database();
          $query = $database->select('migrate_map_upgrade_d7_video', 'map')
            ->fields('map');
          $query->condition("map.sourceid1", $anchor_html);
          /**
           * We need to do a fetchAll since there could be more than one result.
           * The database will do a **case insensitive** search since the field
           * is not stored as BINARY.
           */
          $results = $query->execute()->fetchAll();
          $vid_id = '';

          // Iterate through the results and find the case sensitive, exact
          // match for our anchor.
          foreach ($results as $result) {
            if ($result->sourceid1 == $anchor_html) {
              $vid_id = $result->destid1;
            }
          }

          // Load the Video Media Entity so we can extract the uuid.
          $video = Media::load($vid_id);

          // Create the video embed element.
          $drupalEntity = $dom->createElement('drupal-entity');
          $drupalEntity->setAttribute('data-embed-button', 'video');
          $drupalEntity->setAttribute('data-entity-embed-display', 'view_mode:media.full');
          $drupalEntity->setAttribute('data-entity-type', 'media');
          $drupalEntity->setAttribute('data-entity-uuid', $video->uuid());

          // Add this anchor to the remove array. More on this below.
          $anchorsToRemove[] = $anchor;

          /**
           * Insert the new video embed element directly before the anchor.
           * ReplaceChild doesn't work properly here. It will screw up the
           * foreach iterator. This is a well known issue with iterating
           * DOMNodeList objects.
           */
          $anchor->parentNode->insertBefore($drupalEntity, $anchor);
        }
      }
    }

    /**
     * We can't remove the anchor by using ReplaceChild in the DOMNodeList
     * foreach loop. So we have to store them in an array, which can now be
     * iterated to remove all the anchors that have been replaced by video embed
     * elements.
    */
    foreach ($anchorsToRemove as $anchor) {
      $anchor->parentNode->removeChild($anchor);
    }

    // Convert the modified DOM object to an HTML snippet and return.
    $value['value'] = HTML::serialize($dom);
    return $value;
  }
}
