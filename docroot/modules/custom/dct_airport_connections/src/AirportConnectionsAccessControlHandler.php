<?php

namespace Drupal\dct_airport_connections;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class AirportConnectionsAccessControlHandler.
 *
 * @package Drupal\dct_airport_connections
 */
class AirportConnectionsAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit airport_connections entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete airport_connections entity');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add airport_connections entity');
  }

}
