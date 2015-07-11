<?php

/**
 * @file
 * Contains \Drupal\statistics\StatisticsStorageInterface.
 */

namespace Drupal\statistics;

/**
 * Provides an interface defining Statistics Storage
 * Stores the views per day, total views and timestamp of last view
 * for all nodes on the site.
 */
interface StatisticsStorageInterface {

  /**
   * Count a node view.
   *
   * @param int $nid
   *   The id of the node to count.
   *
   * @return bool
   *   TRUE if the node view has been counted.
   */
  public function recordHit($nid);

  /**
   * Returns the number of times a node has been viewed.
   *
   * @param int $nid
   *   The id of the node to fetch the views for.
   *
   * @return array
   *   An associative array containing:
   *   - totalcount: Integer for the total number of times the node has been
   *     viewed.
   *   - daycount: Integer for the total number of times the node has been viewed
   *     "today". For the daycount to be reset, cron must be enabled.
   *   - timestamp: Integer for the timestamp of when the node was last viewed.
   */
  public function fetchViews($nid);

  /**
   * Returns the number of times a node has been viewed.
   *
   * @param strign $order
   *   The column name to order by.
   *
   * @return array
   *   An ordered array of node ids.
   */
  public function fetchAll($order = 'totalcount', $limit = 5);

  /**
   * Delete counts for a specific node.
   *
   * @param int $nid
   *   The id of the node which views to delete.
   *
   * @return bool
   *   TRUE if the node views have been deleted.
   */
  public function clean($nid);

  /**
   * Returns if reset is needed.
   *
   * @return bool
   *  TRUE if reset is needed.
   */
  public function needsReset();

  /**
   * Resets the day count for all nodes.
   *
   * @return bool
   *  TRUE if the day count is reset.
   */
  public function resetDayCount();

  /**
   * Returns the highest 'totalcount' value.
   *
   * @return int
   *   The highest 'totalcount' value.
   */
  public function maxTotalCount();

}