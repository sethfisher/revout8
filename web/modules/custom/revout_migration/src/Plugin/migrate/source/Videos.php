<?php

namespace Drupal\revout_migration\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;
use Drupal\Component\Utility\Html;

/**
 * Revout migrate videos source plugin.
 *
 * @MigrateSource(
 *   id = "videos"
 * )
 */
class Videos extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Source data is queried from 'field_data_body' table.
    $query = $this->select('field_data_body', 'fdb')
      ->fields('fdb', [
          'entity_id',
          'body_value',
        ]);

    // First we need to parse the nodes and extract all the anchors.
    $nodes = $query->execute()->fetchAll();
    $anchors_query = NULL;
    $anchors_count = 1;
    foreach ($nodes as $node) {
      $entity_id = $node['entity_id'];
      $dom = HTML::load($node['body_value']);
      $anchors = $dom->getElementsByTagName('a');

      /**
       *
       */
      foreach ($anchors as $anchor) {
        $anchor_html = $dom->saveHTML($anchor);
        /**
         * We need to SELECT FROM DUAL because the Drupal Select Object cannot
         * create subqueries as base tables. More (much more) on this in the
         * code below, after the node loop.
         */
        $anchor_query = $this->select('', 'DUAL');
        /**
         * Arguments all get evaluated and set before the query is executed.
         * If you use the same argument variable name multiple times, it will
         * have the same value every time it is used (which is the last value
         * encountered when evaluating the arguments).
         *
         * Therefore, we need different variable names for the entity_id and
         * anchor arguments for each row we create in this "table". Keeping
         * an $anchors_count and appending it to the argument variable names
         * for each row is how we do that.
         */
        $anchor_query->addExpression(":entity_id".$anchors_count, 'entity_id', [':entity_id'.$anchors_count => $entity_id]);
        $anchor_query->addExpression(":anchor".$anchors_count, 'anchor', [':anchor'.$anchors_count => $anchor_html]);

        // If we already have a row in the "table", UNION it with this new one.
        if($anchors_query) {
          $anchors_query = $anchors_query->union($anchor_query);
        }
        // This is the first row, so there is nothing to UNION it with yet.
        else {
          $anchors_query = $anchor_query;
        }
        $anchors_count++;
      }
    }

    // Reset query to only get the entity_id field from fdb.
    $query = $this->select('field_data_body', 'fdb')
      ->fields('fdb', [
        'entity_id',
      ]);

    /**
     * We must run this query first, and then join the UNIONed table we
     * created above to this query. This is because we must use the Drupal
     * Select object since the Migration Source class uses the Select Object to
     * join with the Migration Map tables.
     *
     * The Select object must start with "SELECT FROM". You cannot alias the
     * DUAL table. You have to put the "SELECT ... FROM DUAL in a subquery" and
     * alias the subquery. And the only way we can do that with the Select
     * Object is to join it to a real table.
     *
     * Another method to achieve this migration would be to create an anchors
     * temporary table in the database. Then you could just SELECT FROM that
     * table and there would be no need for joining to an existing table, or
     * SELECTing FROM DUAL.
     *
     * One final method would be to use the prepareQuery method of a Connection
     * Object to give you full control of the query string. Of course this would
     * entail rewriting the Migrate module, since that is expecting you to
     * return a Select Object. Good luck with that.
     *
     * To better understand all this, uncomment the line before return $query
     * and look at the query string that is generated.
     *
     * Also, be sure to read the comments of the getIds method below for more
     * interesting/important information about this migration.
     */
    $query->join($anchors_query, 'anchors', 'anchors.entity_id = fdb.entity_id');
    $query->fields('anchors', ['entity_id', 'anchor']);
    // print_r($query->execute());
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'anchor' => $this->t('anchor'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['anchor']['type'] = 'string';
    /**
     * We also need to use the entity_id as a key because of the Migrate module.
     * The query will return some results where the anchor is duplicated. If we
     * only use the anchor as a key, then the Migrate Map table will be
     * generated with those dupes removed.
     *
     * This sounds like a good thing, but the problem is that the Migrate module
     * uses the count of the returned rows in the initial query as the total
     * number of items; not how many items it actually adds to the Map table.
     * So what happens is all the items in the Map table get processed, but that
     * number doesn't match the number of rows that the query initially
     * returned.
     *
     * Migrate sees this difference as unprocessed rows. This wouldn't be a
     * problem, except for the fact that it treats this migration as unfinished,
     * and all dependent migrations will not be allowed to run.(!?)
     *
     * Therefore, we need the entity_id as part of the key to ensure all these
     * dupes get added to the Migrate Map table, and then it is our
     * responsibility to skip them in our process plugin(s).
     */
    $ids['entity_id']['type'] = 'integer';
    $ids['entity_id']['alias'] = 'fdb';
    return $ids;
  }
}
