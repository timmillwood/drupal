<?php

/**
 * @file
 * Contains \Drupal\system\SystemRequirements.
 */

namespace Drupal\system;

/**
 * Class for helper methods used for the system requirements.
 */
class SystemRequirements {

  /**
   * The minimum PHP version in the lower range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  const MIN_LOW = '5.5.21';

  /**
   * The maximum PHP version in the lower range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  const MAX_LOW = '5.6.0';

  /**
   * The minimum PHP version in the higher range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  const MIN_HIGH = '5.6.5';

  /**
   * Determines whether the passed in PHP version disallows multiple statements.
   *
   * @param string $phpversion
   *
   * @return bool
   */
  public static function phpVersionWithPdoDisallowMultipleStatements($phpversion) {
    return (version_compare($phpversion, self::MIN_LOW, '>=') && version_compare($phpversion, self::MAX_LOW, '<'))
      || version_compare($phpversion, self::MIN_HIGH, '>=');
  }

}
