<?php

/**
 * @file
 * Contains the code for custom Drush commands.
 */

use Drupal\user\Entity\User;

/**
 * Implements hook_drush_command().
 */
function dct_user_drush_command() {
  return [
    'dct-reset-user-roles' => [
      'description' => 'Resets all roles of non-admin users',
    ],
  ];
}

/**
 * Resets all roles of non-admin users.
 */
function drush_dct_user_dct_reset_user_roles() {
  $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties([]);
  foreach ($users as $user) {
    if ($user instanceof User) {
      $roles = $user->getRoles(TRUE);
      echo $user->id() . print_r($roles, TRUE);
      if (!in_array('administrator', $roles)) {
        foreach ($roles as $role) {
          $user->removeRole($role);
        }
        $user->save();
      }
    }
  }
}
