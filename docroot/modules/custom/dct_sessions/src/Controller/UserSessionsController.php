<?php

namespace Drupal\dct_sessions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\dct_sessions\Service\UserSessions;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\User;

/**
 * Class UserSessionsController.
 *
 * @package Drupal\dct_sessions\Controller
 */
class UserSessionsController extends ControllerBase {

  /**
   * The entity_type.manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current_user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The dct_sessions.public_user_roles service.
   *
   * @var \Drupal\dct_sessions\Service\UserSessions
   */
  protected $userSessions;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('dct_sessions.public_user_roles'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, UserSessions $userSessions, AccountInterface $currentUser) {
    $this->entityTypeManager = $entityTypeManager;
    $this->userSessions = $userSessions;
    $this->currentUser = $currentUser;
  }

  /**
   * Gets the content of the 'Be a sprint mentor' page.
   */
  public function content() {
    $user = User::load($this->currentUser->id());
    $user_picture = $this->entityTypeManager->getViewBuilder('user')->viewField($user->get('user_picture'), 'full');

    return [
      '#theme' => 'dct_sessions_user_sessions_page',
      '#user' => $user,
      '#user_picture' => $user_picture,
      '#user_roles' => $this->userSessions->getPublicUserRoles($this->currentUser),
      '#sessions' => $this->userSessions->retrieveUserSessions($this->currentUser),
    ];
  }

}
