<?php

namespace Drupal\dct_sessions\Controller;

use Drupal\contact\MessageInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Session\AccountInterface;
use Drupal\dct_sessions\Service\SessionProposalService;
use Drupal\dct_sessions\Service\UserSessions;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\user\Entity\User;

/**
 * Class UserSessionsController.
 *
 * @package Drupal\dct_sessions\Controller
 */
class UserSessionsController extends ControllerBase {

  /**
   * The form_builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

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
   * The session_proposal service.
   *
   * @var \Drupal\dct_sessions\Service\SessionProposalService
   */
  protected $sessionProposalService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('entity_type.manager'),
      $container->get('dct_sessions.public_user_roles'),
      $container->get('session_proposal'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(FormBuilderInterface $formBuilder, EntityTypeManagerInterface $entityTypeManager, UserSessions $userSessions, SessionProposalService $sessionProposalService, AccountInterface $currentUser) {
    $this->formBuilder = $formBuilder;
    $this->entityTypeManager = $entityTypeManager;
    $this->userSessions = $userSessions;
    $this->sessionProposalService = $sessionProposalService;
    $this->currentUser = $currentUser;
  }

  /**
   * Gets the content of the 'User Sessions' page.
   */
  public function getUserSessionsContent() {
    $user = User::load($this->currentUser->id());
    $user_picture = $this->entityTypeManager->getViewBuilder('user')->viewField($user->get('user_picture'), 'full');

    return [
      '#theme' => 'dct_sessions_user_sessions_page',
      '#user' => $user,
      '#user_picture' => $user_picture,
      '#user_roles' => $this->userSessions->getPublicUserRoles($this->currentUser),
      '#sessions' => $this->userSessions->retrieveUserSessions($this->currentUser),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Gets the content of the 'Session' page.
   *
   * @param \Drupal\contact\MessageInterface $contact_message
   *   The user account.
   *
   * @return array
   *   A render array.
   */
  public function getSessionContent(MessageInterface $contact_message) {

    // Check that the bundle is the correct one.
    if ($contact_message->getEntityTypeId() != 'contact_message' || $contact_message->bundle() != 'session_proposal_form') {
      throw new NotFoundHttpException();
    }

    // Check if the proposal is already accepted.
    if ($contact_message->field_status->value == SessionProposalService::ACCEPTED_SESSION || !empty($contact_message->field_node_id->value)) {
      return $this->redirect('entity.node.canonical', ['node' => $contact_message->field_node_id->value]);
    }

    // Check if user has session permission.
    if (!$this->hasSessionPermission($contact_message)) {
      throw new AccessDeniedHttpException();
    }

    return [
      '#theme' => 'dct_sessions_session_detail_page',
      '#session' => $contact_message,
      '#cache' => [
        'tags' => [
          'contact_message:' . $contact_message->id(),
        ]
      ]
    ];
  }

  /**
   * Gets the sessions page title.
   *
   * @param \Drupal\contact\MessageInterface $contact_message
   *   The user account.
   *
   * @return string
   *   The title of the page.
   */
  public function getSessionTitle(MessageInterface $contact_message) {
    return $contact_message->field_name->value;
  }

  /**
   * Gets the content of the 'Session' page.
   *
   * @param \Drupal\contact\MessageInterface $contact_message
   *   The user account.
   *
   * @return array
   *   A render array.
   */
  public function editSessionProposal(MessageInterface $contact_message) {

    // Check that the bundle is the correct one.
    if ($contact_message->getEntityTypeId() != 'contact_message' || $contact_message->bundle() != 'session_proposal_form') {
      throw new NotFoundHttpException();
    }

    // Check if the proposal is PENDING.
    if ($contact_message->field_status->value != SessionProposalService::PENDING_SESSION || !empty($contact_message->field_node_id->value)) {
      throw new AccessDeniedHttpException();
    }

    // Check if user has session permission.
    if (!$this->hasSessionPermission($contact_message)) {
      throw new AccessDeniedHttpException();
    }

    $form_state = new FormState();

    // Retrieve the display of session_proposal_form form.
    $display = $this->entityTypeManager->getStorage('entity_form_display')
      ->load('contact_message.session_proposal_form.default');

    // Retrieve empty form object and set the display and the entity.
    $formObj = $this->entityTypeManager->getFormObject('contact_message', 'edit');
    $formObj->setFormDisplay($display, $form_state);
    $formObj->setEntity($contact_message);

    return $this->formBuilder->buildForm($formObj, $form_state);
  }

  /**
   * Gets the content of the 'Session' page.
   *
   * @param \Drupal\contact\MessageInterface $contact_message
   *   The user account.
   *
   * @return array
   *   A render array.
   */
  public function getCancelContent(MessageInterface $contact_message) {
    // Check that the bundle is the correct one.
    if ($contact_message->getEntityTypeId() != 'contact_message' || $contact_message->bundle() != 'session_proposal_form') {
      throw new NotFoundHttpException();
    }

    // Check if the proposal is PENDING.
    if ($contact_message->field_status->value != SessionProposalService::PENDING_SESSION || !empty($contact_message->field_node_id->value)) {
      throw new AccessDeniedHttpException();
    }

    // Check if user has session permission.
    if (!$this->hasSessionPermission($contact_message)) {
      throw new AccessDeniedHttpException();
    }

    // Gets the confirmation form.
    $form = $this->formBuilder->getForm('\Drupal\dct_sessions\Form\CancelSessionProposalForm', $contact_message->id());

    return $form;
  }

  /**
   * Checks if the current owner has access to the session..
   *
   * @param \Drupal\contact\MessageInterface $contact_message
   *   The user account.
   *
   * @return bool
   *   If the user has access or not.
   */
  private function hasSessionPermission(MessageInterface $contact_message) {
    if (in_array('administrator', $this->currentUser->getRoles()) || in_array('organizer', $this->currentUser->getRoles())) {
      return TRUE;
    }

    // Only users in the session track have access.
    $allowed_users = $contact_message->get('field_user')->getValue();
    $allow_user = $this->currentUser->id() == $contact_message->uid->target_id ? TRUE : FALSE;
    foreach ($allowed_users as $allowed_user) {
      if ($allowed_user['target_id'] == $this->currentUser->id()) {
        $allow_user = TRUE;
      }
    }

    return $allow_user;
  }

}
