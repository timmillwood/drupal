<?php

/**
 * @file
 * Contains \Drupal\statistics\Tests\StatisticsUpdateTest.
 */

namespace Drupal\statistics\Tests;

use Drupal\system\Tests\Update\UpdatePathTestBase;

/**
 * Tests the upgrade path for Statistics.
 *
 * @see https://www.drupal.org/node/2421663
 *
 * @group Update
 */
class StatisticsUpdateTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'statistics'];

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->databaseDumpFiles = [
      __DIR__ . '/../../../system/tests/fixtures/update/drupal-8.bare.standard.php.gz',
    ];
    parent::setUp();
  }

  /**
   * Tests the update path for the Statistics module.
   */
  public function testUpdate() {
    // Check the Schema version.
    $schema_version = drupal_get_installed_schema_version('statistics');
    $this->assertEqual($schema_version, '8001', 'Schema version is 8001');

    // Set schema version to 0 for statistics module so all update hooks run.
    drupal_set_installed_schema_version('statistics', '8000');

    // Check the Schema version.
    $schema_version = drupal_get_installed_schema_version('statistics');
    $this->assertEqual($schema_version, '8000', 'Schema version is 8000');

    // Make sure the node module was enabled in the first place.
    $this->assertTrue(\Drupal::moduleHandler()->moduleExists('node'), 'Node module is enabled');

    // Make sure the test setup enabled the statistics module.
    $this->assertTrue(\Drupal::moduleHandler()->moduleExists('statistics'), 'Statistics module is enabled');

    // Uninstall the node module.
    \Drupal::service('module_installer')->uninstall(array('node'), FALSE);

    // Make sure the node module was in fact disabled.
    $this->assertFalse(\Drupal::moduleHandler()->moduleExists('node'), 'Node module is disabled');

    // Run the update hooks.
    $this->runUpdates();
    //statistics_update_8001();

    // Check the Schema version.
    $schema_version = drupal_get_installed_schema_version('statistics');
    $this->assertEqual($schema_version, '8001', 'Schema version is 8001');

    // Make sure the statistics module was disabled by the update hook.
    $this->assertFalse(\Drupal::moduleHandler()->moduleExists('statistics'), 'Statistics module is disabled');
  }
}
