<?php

namespace Drupal\dct_sessions\Service;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\Entity\Role;

/**
 * Class UserSessions.
 *
 * @package Drupal\dct_sessions\Service
 */
class UserSessions {

  /**
   * The entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * SessionProposalService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Returns the public user roles as a string.
   *
   * @param AccountInterface $user
   *   The user object.
   * @return string public user roles
   */
  public static function getPublicUserRoles(AccountInterface $user){
    $roles = $user->getRoles();
    $private_roles = ['anonymous', 'authenticated', 'administrator'];
    $display_roles = [];
    foreach ($roles as $role) {
      if (!in_array($role, $private_roles)) {
        $role = Role::load($role);
        $display_roles[] = $role->get('label');
      }
    }

    return implode(', ', $display_roles);
  }

  /**
   * Retrieves user session contact submissions.
   *
   * @param AccountInterface $user
   *   The user object.
   * @return array
   *   The session contact submissions information.
   */
  public function retrieveUserSessions(AccountInterface $user) {
    // Retrieve the display of sponsors_contact_form form.
    $submissions =  $this->entityTypeManager->getStorage('contact_message')
      ->loadByProperties(['contact_form' => 'session_proposal_form', 'field_user' => [$user->id()]]);

    $submissions_information = [];
    foreach ($submissions as $id => $submission) {
      $submissions_information[$id]['title'] = $submission->field_name->value;
    }

    return $submissions_information;
  }

}
