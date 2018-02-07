<?php

namespace Drupal\dct_sessions\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;

/**
 * Class SessionProposalService.
 *
 * @package Drupal\dct_sessions\Service
 */
class SessionProposalService {

  /**
   * Available statuses for session contact messages.
   */
  const PENDING_SESSION = 'PENDING';
  const ACCEPTED_SESSION = 'ACCEPTED';
  const CANCELED_SESSION = 'CANCELED';
  const REJECTED_SESSION = 'REJECTED';

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
   * Saves the session proposal message as a node.
   *
   * @param int $contact_message_id
   *   The id of the contact message entity.
   */
  public function saveSessionProposal($contact_message_id) {
    // Gets the entity with this id.
    $message_entity = $this->getEntity($contact_message_id);

    // Creates a node with the values of the contact message entity.
    $node = Node::create([
      'type' => 'session',
      'uid' => $message_entity->get('uid')->getValue(),
      'field_subtitle' => $message_entity->get('name'),
      'field_email' => $message_entity->get('mail'),
      'title' => $message_entity->get('field_name'),
      'field_list_select' => $message_entity->get('field_radio_button')->getValue(),
      'field_plain_description' => $message_entity->get('field_description')->getValue(),
      'field_select' => $message_entity->get('field_select')->getValue(),
      'field_select_list' => $message_entity->get('field_duration')->getValue(),
      'field_taxonomy_reference' => $message_entity->get('field_taxonomy_reference')->getValue(),
      'field_session_type' => $message_entity->get('field_select_list')->getValue(),
      'field_files' => $message_entity->get('field_file_upload')->getValue(),
      'field_speakers' => $message_entity->get('field_user')->getValue(),
      'field_long_description' => $message_entity->get('field_long_description')->getValue(),
    ]
    );
    $node->save();

    // Update the field_status with the ACCEPTED status.
    $message_entity->field_status->value = self::ACCEPTED_SESSION;
    // Saves a reference to the newly created node in the message entity.
    $message_entity->field_node_id->value = $node->id();
    $message_entity->save();
  }

  /**
   * Changes the status of the session proposal to REJECTED.
   *
   * @param int $contact_message_id
   *   The id of the contact message entity.
   */
  public function rejectSessionProposal($contact_message_id) {
    // Gets the contact_message entity with this id.
    $message_entity = $this->getEntity($contact_message_id);

    // Update the field_status with the REJECTED status.
    $message_entity->field_status->value = self::REJECTED_SESSION;
    $message_entity->save();
  }

  /**
   * Changes the status of the session proposal to CANCELED.
   *
   * @param int $contact_message_id
   *   The id of the contact message entity.
   */
  public function cancelSessionProposal($contact_message_id) {
    // Gets the contact_message entity with this id.
    $message_entity = $this->getEntity($contact_message_id);

    // Update the field_status with the REJECTED status.
    $message_entity->field_status->value = self::CANCELED_SESSION;
    $message_entity->save();
  }

  /**
   * Gets the contact message entity.
   *
   * @param string $entity_id
   *   The id of the entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The contact message entity.
   */
  public function getEntity($entity_id) {
    return $this->entityTypeManager->getStorage('contact_message')
      ->load($entity_id);
  }

}
