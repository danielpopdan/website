<?php

namespace Drupal\dct_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the review pane.
 *
 * @CommerceCheckoutPane(
 *   id = "ticket_information",
 *   label = @Translation("Ticket information"),
 *   default_step = "ticket_information",
 * )
 */
class TicketInformation extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $order_items = $this->order->order_items;
    foreach ($order_items as $delta => $item) {
      $order_item = $item->entity;
      /* @var $order_item \Drupal\commerce_order\Entity\OrderItemInterface */
      $pane_form['order_item'][$delta] = [
        '#type' => 'fieldset',
        '#title' => $order_item->getTitle(),
        '#tree' => TRUE,
      ];
      $quantity = (int) $order_item->getQuantity();
      for ($number = 0; $number < $quantity; $number++) {
        $recipient_item_field = $order_item->field_recipients->get($number);
        $recipient = isset($recipient_item_field) ? $recipient_item_field->value : '';
        $pane_form['order_item'][$delta][$number] = [
          '#type' => 'email',
          '#title' => t('Ticket recipient email #%number', ['%number' => $number + 1]),
          '#default_value' => $recipient,
        ];
      }
    }

    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    $values = $form_state->getValue('ticket_information');
    foreach ($values['order_item'] as $delta => $item_values) {
      /* @var $order_item_entity \Drupal\commerce_order\Entity\OrderItemInterface */
      $order_item_entity = $this->order->order_items[$delta]->entity;
      $order_item_entity->set('field_recipients', $item_values);
      $order_item_entity->save();
    }

  }

}
