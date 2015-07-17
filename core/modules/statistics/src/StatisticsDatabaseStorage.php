<?php
/**
 * @file
 * Contains \Drupal\statistics\StatisticsDatabaseStorage.
 */

namespace Drupal\statistics;

use Drupal\Core\Database\Connection;
use Drupal\Core\State\StateInterface;

/**
 * Provides the default database storage backend for statistics module.
 */
class StatisticsDatabaseStorage implements StatisticsStorageInterface {

  /**
  * The database connection used.
  *
  * @var \Drupal\Core\Database\Connection
  */
  protected $connection;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
  * Construct the statistics storage.
  *
  * @param \Drupal\Core\Database\Connection $connection
  *   The database connection for the node view storage.
  * @param \Drupal\Core\State\StateInterface $state
  *   The state service.
  */
  public function __construct(Connection $connection, StateInterface $state) {
    $this->connection = $connection;
    $this->state = $state;
  }

  /**
  * {@inheritdoc}
  */
  public function recordHit($nid) {
    return (bool) $this->connection->merge('node_counter')
      ->key('nid', $nid)
      ->fields(array(
        'daycount' => 1,
        'totalcount' => 1,
        'timestamp' => REQUEST_TIME,
      ))
      ->expression('daycount', 'daycount + 1')
      ->expression('totalcount', 'totalcount + 1')
      ->execute();
  }

  /**
  * {@inheritdoc}
  */
  public function fetchViews($nid) {
    // Retrieve an array, which includes totalcount, daycount, and timestamp.
    return $this->connection->select('node_counter', 'nc')
      ->fields('nc', array('totalcount', 'daycount', 'timestamp'))
      ->condition('nid', $nid, '=')->execute()->fetchAssoc();
  }

  /**
  * {@inheritdoc}
  */
  public function fetchAll($order = 'totalcount', $limit = 5) {
    if (in_array($order, array('totalcount', 'daycount', 'timestamp'))) {
      return $this->connection->select('node_counter', 'nc')
        ->fields('nc', array('nid'))
        ->orderBy($order, 'DESC')->range(0, $limit)
        ->execute()->fetchCol();
    }
    return FALSE;
  }

  /**
  * {@inheritdoc}
  */
  public function clean($nid) {
    return (bool) $this->connection->delete('node_counter')
      ->condition('nid', $nid)
      ->execute();
  }

  /**
  * {@inheritdoc}
  */
  public function needsReset() {
    $statistics_timestamp = $this->state->get('statistics.day_timestamp') ?: 0;
    return (REQUEST_TIME - $statistics_timestamp) >= 86400;
  }

  /**
  * {@inheritdoc}
  */
  public function resetDayCount() {
    $this->state->set('statistics.day_timestamp', REQUEST_TIME);
    return (bool) $this->connection->update('node_counter')
      ->fields(array('daycount' => 0))
      ->execute();
  }

  /**
  * {@inheritdoc}
  */
  public function maxTotalCount() {
    $query = $this->connection->select('node_counter', 'nc');
    $query->addExpression('MAX(totalcount)');
    $max_total_count = (int)$query->execute()->fetchField();
    $this->state->set('statistics.node_counter_scale', 1.0 / max(1.0, $max_total_count));
    return $max_total_count;
  }

}
