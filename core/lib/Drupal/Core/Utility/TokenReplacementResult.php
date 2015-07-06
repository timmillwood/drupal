<?php

/**
 * @file
 * Contains \Drupal\Core\Token\Utility\TokenReplacementResult.
 */

namespace Drupal\Core\Token\Utility;

use Drupal\Core\Cache\CacheableMetadata;

/**
 * Used to return values token replacement operation.
 *
 * Most token replacements will take a token of the form [base:token] and
 * replace this with a string calculated at run-time. However some of these may
 * need to pass cacheability information to the caller. E.g. using the
 * [user:name] token in a node-body needs to add cacheability information for
 * the user to the node's cacheability data.
 *
 * This value object is used when a a token replacement needs one of the
 * following:
 * - To declare cache tags that the replacement text depends upon, so when
 *   any of those cache tags is invalidated, the replacement text should also be
 *   invalidated.
 * - Declare cache context to vary by, e.g. 'language' to do language-specific
 *   tokening.
 * - Declare a maximum age for the replacement text.
 *
 * In case a token needs one or more of these advanced use cases, it can use
 * the additional methods available.
 *
 * The typical use case:
 * @code
 * public function process($text, $langcode) {
 *   // Determine the value of $replacement.
 *
 *   return new TokenReplacementResult($replacement);
 * }
 * @endcode
 *
 * The advanced use cases:
 * @code
 * public function process($text, $langcode) {
 *   // Determine the value of $replacement.
 *
 *   $result = new TokenReplacementResult($replacement);
 *
 *   // Associate cache contexts to vary by.
 *   $result->setCacheContexts(['language']);
 *
 *   // Associate cache tags to be invalidated by.
 *   $result->setCacheTags($node->getCacheTags());
 *
 *   // Associate a maximum age.
 *   $result->setCacheMaxAge(300); // 5 minutes.
 *
 *   return $result;
 * }
 * @endcode
 */
class TokenReplacementResult extends CacheableMetadata {

  /**
   * The replacement text.
   *
   * @see \Drupal\token\Plugin\TokenInterface::process()
   *
   * @var string
   */
  protected $replacementText;

  /**
   * Constructs a TokenReplacementResult object.
   *
   * @param string $replacement_text
   *   The token replacement value.
   */
  public function __construct($replacement_text) {
    $this->replacementText = $replacement_text;
  }

  /**
   * Gets the replacement text of the token.
   *
   * @return string
   *   The underlying replacement text for the token.
   */
  public function getReplacementText() {
    return $this->replacementText;
  }

  /**
   * Gets the processed text.
   *
   * @return string
   *   The string representation of this token.
   */
  public function __toString() {
    return $this->getReplacementText();
  }

  /**
   * Sets the replacement text.
   *
   * @param string $replacement_text
   *   The underlying string representation of this replacement.
   *
   * @return $this
   */
  public function setReplacementText($replacement_text) {
    $this->replacementText = $replacement_text;
    return $this;
  }

}
