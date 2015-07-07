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
  public static $min_low = '5.5.21';

  /**
   * The maximum PHP version in the lower range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  public static $max_low = '5.6.0';

  /**
   * The minimum PHP version in the higher range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  public static $min_high = '5.6.5';

  /**
   * Determines whether the passed in PHP version disallows multiple statements.
   *
   * @param string $phpversion
   *
   * @return bool
   */
  public static function phpVersionWithPdoDisallowMultipleStatements($phpversion) {
    return (version_compare($phpversion, self::$min_low, '>=') && version_compare($phpversion, self::$max_low, '<'))
      || version_compare($phpversion, self::$min_high, '>=');
  }

}
