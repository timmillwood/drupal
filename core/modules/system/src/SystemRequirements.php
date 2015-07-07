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
  public static $minLow = '5.5.21';

  /**
   * The maximum PHP version in the lower range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  public static $maxLow = '5.6.0';

  /**
   * The minimum PHP version in the higher range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  public static $minHigh = '5.6.5';

  /**
   * Determines whether the passed in PHP version disallows multiple statements.
   *
   * @param string $phpversion
   *
   * @return bool
   */
  public static function phpVersionWithPdoDisallowMultipleStatements($phpversion) {
    return (version_compare($phpversion, self::$minLow, '>=') && version_compare($phpversion, self::$maxLow, '<'))
      || version_compare($phpversion, self::$minHigh, '>=');
  }

}
