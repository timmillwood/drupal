<?php

namespace Drupal\content_moderation;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Validates whether a certain state transition is allowed.
 */
interface StateTransitionValidationInterface {

  /**
   * Gets a list of states a user may transition an entity to.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to be transitioned.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The account that wants to perform a transition.
   *
   * @return \Drupal\content_moderation\Entity\ModerationState[]
   *   Returns an array of States to which the specified user may transition the
   *   entity.
   */
  public function getValidTransitionTargets(ContentEntityInterface $entity, AccountInterface $user);

  /**
   * Gets a list of transitions that are legal for this user on this entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to be transitioned.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The account that wants to perform a transition.
   *
   * @return \Drupal\content_moderation\Entity\ModerationStateTransition[]
   */
  public function getValidTransitions(ContentEntityInterface $entity, AccountInterface $user);

  /**
   * Determines if a user is allowed to transition from one state to another.
   *
   * This method will also return FALSE if there is no transition between the
   * specified states at all.
   *
   * @param string $from
   *   The origin state machine name.
   * @param string $to
   *   The destination state machine name.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user to validate.
   *
   * @return bool
   *   TRUE if the given user may transition between those two states.
   */
  public function userMayTransition($from, $to, AccountInterface $user);

  /**
   * Determines a transition allowed.
   *
   * @param string $from
   *   The from state.
   * @param string $to
   *   The to state.
   *
   * @return bool
   *   Is the transition allowed.
   */
  public function isTransitionAllowed($from, $to);

}
