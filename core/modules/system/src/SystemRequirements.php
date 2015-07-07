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
   * The minimum PHP version in the lower range
   */
  public static $min_low = '5.5.21';
  
  /**
   * The maximum PHP version in the lower range
   */
  public static $max_low = '5.6.0';
  
  /**
   * The minimum PHP version in the higher range
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
    // PDO::MYSQL_ATTR_MULTI_STATEMENTS was introduced in PHP versions 5.5.21
    // and 5.6.5.
    return (version_compare($phpversion, self::$min_low, '>=') && version_compare($phpversion, self::$max_low, '<'))
      || version_compare($phpversion, self::$min_high, '>=');
  }

}
