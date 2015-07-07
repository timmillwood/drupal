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
  const PHP_MIN_LOW = '5.5.21';

  /**
   * The maximum PHP version in the lower range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  const PHP_MAX_LOW = '5.6.0';

  /**
   * The minimum PHP version in the higher range for the introduction
   * of PDO::MYSQL_ATTR_MULTI_STATEMENTS.
   */
  const PHP_MIN_HIGH = '5.6.5';

  /**
   * Determines whether the passed in PHP version disallows multiple statements.
   *
   * @param string $phpversion
   *
   * @return bool
   */
  public static function phpVersionWithPdoDisallowMultipleStatements($phpversion) {
    return (version_compare($phpversion, self::PHP_MIN_LOW, '>=') && version_compare($phpversion, self::PHP_MAX_LOW, '<'))
      || version_compare($phpversion, self::PHP_MIN_HIGH, '>=');
  }
  
  /**
   * Current php version without extra.
   *
   * @return string
   */
  public static function phpVer() {
    return str_replace(PHP_EXTRA_VERSION, "", PHP_VERSION);
  }

}
