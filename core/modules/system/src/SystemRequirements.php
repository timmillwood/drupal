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
  
  public static $min_low = '5.5.21';
  public static $max_low = '5.6.0';
  public static $min_high = '5.6.22';

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
