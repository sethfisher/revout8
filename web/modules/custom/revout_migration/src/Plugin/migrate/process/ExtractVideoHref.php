<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\ExtractVideoHref.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\media\Entity\Media;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity;

/**
 * This plugin extracts hrefs from anchors and uses as the Video Embed field
 * of Video entities.
 *
 * @MigrateProcessPlugin(
 *   id = "extract_video_href"
 * )
 */
class ExtractVideoHref extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $dom = HTML::load($value);
    $anchors = $dom->getElementsByTagName('a');
    $href = '';

    /**
     * There will only be one anchor, but calling the above DOM method and
     * "looping" through a one result Object is the easiest way.
     */
    foreach ($anchors as $anchor) {
      $href = $anchor->getAttribute('href');

      // The videoId comes between embed/ and ? in all Revout7 video links.
      $videoIdStart = strpos($href, 'embed/') + 6;
      $videoIdLength = strpos($href, '?') - $videoIdStart;
      $videoId = substr($href, $videoIdStart, $videoIdLength);

      /**
       * The Revout8 method of embedding video links requires switching
       * from /embed/$videoId to /watch?v=$videoId. We first switch ? to &
       * so we end up with ?v=$video& and then the rest of the query params.
       */
      $href = str_replace('?', '&', $href);
      $href = str_replace('/embed/', '/watch?v=', $href);
      // We need to use t instead of start as the video start time param.
      $href = str_replace('&start=', '&t=', $href);

      // Create a static array for all the videos in both playlists.
      static $playlists;
      if (!isset($playlists)) {
        // Socialism & OITNB Music
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['nJ4ro4MssEQ'] = 0;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['qOMBBoK9lFQ'] = 1;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['h-1UFeTffvI'] = 2;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['h_j_48G2L_o'] = 3;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['ed9X9ovQ8yk'] = 4;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['MXrfVduJY-A'] = 5;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['kdFnoYvmZtA'] = 6;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['-8IUbVQRqAk'] = 7;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['iRmZ1jh14uU'] = 8;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['0DsXxiIrPVA'] = 9;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['nIZWMs1ydzM'] = 10;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['GWYDbvFH15g'] = 11;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['RqfmnzHHPtk'] = 12;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['kICLkq7jCCc'] = 13;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['i68Mtv5YJbM'] = 14;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['Yq9P1nu6CY8'] = 15;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['2fNObJk2tGY'] = 16;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['SFvkgW0ZRN0'] = 17;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['fXsR2ws90b8'] = 18;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['-Btca1VvOwg'] = 19;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['kOINSUWOqyo'] = 20;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['fVqr1s7QuOw'] = 21;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['QKQYSjrOsSw'] = 22;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['sGjfLuVn44Q'] = 23;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['hYa4gZ59-1c'] = 24;
        $playlists['PLePIGfbGDjNh9eSt5xnUsBp07GuD88xq1']['aTL4qIIxg8A'] = 25;

        // Socialism & OITNB "Seth" Videos (for lack of better term)
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['y3sD1dDyOOw'] = 0;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['YPbNNBLCfvQ'] = 1;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['--kti0m5cGQ'] = 2;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['dMGlQlvB9Fs'] = 3;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['Sc8hmpHOV4s'] = 4;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['rNaQCUL7AbY'] = 5;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['cIOANi4kyzY'] = 6;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['ct7QNErG40A'] = 7;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['-PikODkNDHo'] = 8;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['QotSGZtu-ls'] = 9;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['MbFqv_TTM_0'] = 10;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['KINf_PmU56Q'] = 11;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['TKPd4QoB9uQ'] = 12;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['-RGY0biZmtA'] = 13;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['XLrkyhmp5W4'] = 14;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['80OhD23k0BE'] = 15;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['cu2c55RHoYU'] = 16;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['mC9dnuGtLiM'] = 17;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['C1cQGBeYHmw'] = 18;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['ft1VrIKoZfE'] = 19;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['BGFvXYl35S8'] = 20;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['52MVEip9yz0'] = 21;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['NCI5mPDr1lE'] = 22;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['pxO_8oU81U4'] = 23;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['9o8PFWMV2to'] = 24;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['PRK6OJqyylM'] = 25;
        $playlists['PLePIGfbGDjNgL8ceC8_jNZqBQz9diYABu']['G1Hdg6L82fA'] = 26;
      }

      // Extract the playlistId where it exists.
      $playlistIdStart = strpos($href, '&list=') + 6;
      // &autoplay always comes after the playlistId in the Revout7 links,
      // so it can be used here to buttress the end of the playlistId.
      $playlistIdLength = strpos($href, '&autoplay') - $playlistIdStart;
      $playlistId = substr($href, $playlistIdStart, $playlistIdLength);

      $playlistIndex = $playlists[$playlistId][$videoId];

      /**
       * Add the index of the video in the playlist to the end of query string.
       * We could check if there is no index and then not append the parameter
       * (i.e. this isn't a playlist link). But there's no harm in adding the
       * empty parameter.
       */
      $href = $href . '&index=' . $playlistIndex;
    }
    return $href;
  }
}
