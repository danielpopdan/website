<?php

namespace Drupal\dct_commerce\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the ticket entity class.
 *
 * @ContentEntityType(
 *   id = "dct_commerce_ticket",
 *   label = @Translation("DCT Ticket"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *   },
 *   base_table = "dct_ticket",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "code"
 *   }
 * )
 */
class Ticket extends ContentEntityBase {

  /**
   * Gets the ticket code.
   *
   * @return string
   */
  public function getCode() {
    return $this->code->getValue();
  }

  /**
   * Gets the order item of this ticket.
   *
   * @return \Drupal\commerce_order\Entity\OrderItemInterface
   */
  public function getOrderItem() {
    return $this->order_item->getEntity();
  }

  /**
   * Gets the ticket buyer.
   *
   * @return \Drupal\user\UserInterface
   */
  public function getBuyer() {
    return $this->buyer->getEntity();
  }

  /**
   * Checks if this ticket was redeemed.
   *
   * @return bool
   */
  public function isRedeemed() {
    return (bool) $this->redeemer;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setSetting('max_length', 255)
      ->setReadOnly(TRUE);

    $fields['order_item'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Order'))
      ->setDescription(t('The orderitem on which the ticket was bought.'))
      ->setSetting('commerce_order_item', 'user');

    $fields['buyer'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Buying user'))
      ->setDescription(t('The user that bought the ticket.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\commerce_log\Entity\Log::getCurrentUserId');

    $fields['redeemer'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Redeeming user'))
      ->setDescription(t('The user that redeemed the ticket.'))
      ->setSetting('target_type', 'user')
      ->setDefaultValue(NULL);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Activated on'))
      ->setDescription(t('The time that the ticket was redeemed.'));

    $fields['activated'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Activated on'))
      ->setDescription(t('The time that the ticket was redeemed.'));

    return $fields;
  }

}
