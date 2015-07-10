<?php

namespace Drupal\statistics;

use Drupal\Core\Database\Connection;
use Drupal\Core\State\StateInterface;

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
    // Retrieve an array with both totalcount, daycount and timestamp.
    return $this->connection->select('node_counter', 'nc')
      ->fields('nc', array('totalcount', 'daycount', 'timestamp'))
      ->condition('nid', $nid, '=')->execute()->fetchAssoc();
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

  /**
   * Returns the most viewed content of all time, today, or the last-viewed node.
   *
   * @param string $dbfield
   *   The database field to use, one of:
   *   - 'totalcount': Integer that shows the top viewed content of all time.
   *   - 'daycount': Integer that shows the top viewed content for today.
   *   - 'timestamp': Integer that shows only the last viewed node.
   * @param int $dbrows
   *   The number of rows to be returned.
   *
   * @return SelectQuery|FALSE
   *   A query result containing the node ID, title, user ID that owns the node,
   *   and the username for the selected node(s), or FALSE if the query could not
   *   be executed correctly.
   */
  public function statisticsTitleList($dbfield, $dbrows) {
    if (in_array($dbfield, array('totalcount', 'daycount', 'timestamp'))) {
      $query = $this->connection->select('node_field_data', 'n');
      $query->addTag('node_access');
      $query->join('node_counter', 's', 'n.nid = s.nid');
      $query->join('users_field_data', 'u', 'n.uid = u.uid');

      return $query
        ->fields('n', array('nid', 'title'))
        ->fields('u', array('uid', 'name'))
        ->condition($dbfield, 0, '<>')
        ->condition('n.status', 1)
        // @todo This should be actually filtering on the desired node status
        // field language and just fall back to the default language.
        ->condition('n.default_langcode', 1)
        ->condition('u.default_langcode', 1)
        ->orderBy($dbfield, 'DESC')
        ->range(0, $dbrows)
        ->execute();
    }
    return FALSE;
  }
}
