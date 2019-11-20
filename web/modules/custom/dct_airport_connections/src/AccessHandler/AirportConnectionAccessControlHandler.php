<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections\AccessHandler;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class AirportConnectionAccessControlHandler.
 *
 * @package Drupal\dct_airport_connections\AccessHandler
 */
class AirportConnectionAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResultInterface {
    switch ($operation) {
      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit airport_connection entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete airport_connection entity');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entityBundle = NULL): AccessResultInterface {
    return AccessResult::allowedIfHasPermission($account, 'add airport_connection entity');
  }

}
