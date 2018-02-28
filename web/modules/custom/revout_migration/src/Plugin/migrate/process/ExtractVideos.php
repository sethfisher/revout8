<?php
/**
 * @file
 * Contains \Drupal\revout_migration\Plugin\migrate\process\ExtractVideos.
 */
namespace Drupal\revout_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\media\Entity\Media;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity;

/**
 * This plugin extracts videos from node body and creates media entities of
 * Video bundle.
 *
 * @MigrateProcessPlugin(
 *   id = "extract_videos"
 * )
 */
class ExtractVideos extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $dom = HTML::load($value['value']);
    $anchors = $dom->getElementsByTagName('a');
    foreach ($anchors as $anchor) {
      if ($anchor->hasAttribute('class')) {
        $class = $anchor->getAttribute('class');
        if (strpos($class, 'colorbox-load') !== FALSE) {
          $class = str_replace('colorbox-load', 'use-ajax', $class);
          $anchor->setAttribute('class', $class);
          $anchor->setAttribute('data-dialog-type', 'colorbox');

          $href = $anchor->getAttribute('href');

          $videoIdStart = strpos($href, 'embed/') + 6;
          $videoIdLength = strpos($href, '?') - $videoIdStart;

          $videoId = substr($href, $videoIdStart, $videoIdLength);

          $href = str_replace('?', '&', $href);
          $href = str_replace('/embed/', '/watch?v=', $href);
          $href = str_replace('&start=', '&t=', $href);

          static $playlists;
          if (!isset($playlists)) {
            // Music
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

            // Socialism & OITNB
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

          $playlistIdStart = strpos($href, '&list=') + 6;
          $playlistIdLength = strpos($href, '&autoplay') - $playlistIdStart;

          $playlistId = substr($href, $playlistIdStart, $playlistIdLength);

          $playlistIndex = $playlists[$playlistId][$videoId];

          $href = $href . '&index=' . $playlistIndex;

          $videos = \Drupal::entityTypeManager()
            ->getStorage('media')
            ->loadByProperties(['field_media_video_embed_field' => [
                'value' => $href]]);

          $video = reset($videos);

          if (!$video) {
            $video = Media::create([
              'bundle' => 'video',
              'name' => $videoId,
              'field_media_video_embed_field' => [
                'value' => $href,
              ],
            ]);
            $video->save();
          }

          $anchor->setAttribute('href', '/media/' . $video->id());
        }
      }
    }

    $value['value'] = HTML::serialize($dom);
    return $value;
  }
}
