<?php

namespace Drupal\statistics;

use Drupal\Core\Database\Connection;

class StatisticsDatabaseStorage implements StatisticsStorageInterface {

  /**
  * The database connection used.
  *
  * @var \Drupal\Core\Database\Connection
  */
  protected $connection;

  /**
  * Construct the statistics storage.
  *
  * @param \Drupal\Core\Database\Connection $connection
  *   The database connection for the node view storage.
  */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
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
    if ($nid > 0) {
      // Retrieve an array with both totalcount and daycount.
      return $this->connection->select('node_counter', 'nc')
        ->fields('nc', array('totalcount', 'daycount', 'timestamp'))
          ->condition('nid', $nid, '=')->execute()->fetchAssoc();
    }
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
  public function resetDayCount() {
    return (bool) $this->connection->update('node_counter')
      ->fields(array('daycount' => 0))
        ->execute();
  }

  /**
  * {@inheritdoc}
  */
  public function maxTotalCount() {
    $q = $this->connection->select('node_counter', 'nc');
    $q->addExpression('MAX(totalcount)');
    return (int) $q->execute()->fetchField();
  }

  /**
  * {@inheritdoc}
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
      //   field language and just fall back to the default language.
      ->condition('n.default_langcode', 1)
        ->condition('u.default_langcode', 1)
          ->orderBy($dbfield, 'DESC')
            ->range(0, $dbrows)
              ->execute();
    }
    return FALSE;
  }
}
