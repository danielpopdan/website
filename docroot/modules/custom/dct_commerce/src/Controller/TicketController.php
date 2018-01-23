<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Component\Utility\Random;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use RuntimeException;

class TicketController extends ControllerBase implements TicketControllerInterface {

  /**
   * The maximum number of times generateTicketCode() can loop.
   */
  const MAXIMUM_TRIES = 100;

  /**
   * The ticket storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $ticketStorage = NULL;

  /**
   * {@inheritdoc}
   */
  public function handlePaymentSuccess(OrderInterface $order) {

    // Iterate over the order items.
    foreach ($order->order_items as $item) {
      /* @var $order_item \Drupal\commerce_order\Entity\OrderItemInterface */
      $order_item = $item->entity;
      $quantity = (int) $order_item->getQuantity();
      $tickets = [];

      // Generate one ticket for each item mentioned in the quantity.
      for ($number = 0; $number < $quantity; $number++) {
        $user = $this->currentUser();
        $ticket = $this->createTicket($user, $order_item);
        $ticket->save();

        $tickets[] = $ticket;
      }

      // Add the tickets to the order item.
      $order_item->set('field_tickets', $tickets);
      $order_item->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createTicket(AccountInterface $creator, OrderItemInterface $orderItem) {

    // TODO: Have this made configurable somewhere.
    $code = $this->generateTicketCode(8, 'DCT2018-');
    $values = [
      'code' => $code,
      'order_item' => $orderItem->id(),
      'buyer' => $creator->id(),
    ];

    /* @var $ticket \Drupal\dct_commerce\Entity\TicketInterface */
    $ticket = $this->getTicketStorage()->create($values);

    return $ticket;
  }

  /**
   * {@inheritdoc}
   */
  public function generateTicketCode($codeLength = 8, $prefix = NULL, $suffix = NULL) {
    $random = new Random();

    for ($counter = 0; $counter < static::MAXIMUM_TRIES; $counter++) {
      $code = $prefix . strtoupper($random->name($codeLength)) . $suffix;
      if (empty($this->getTicketByCode($code))) {
        return $code;
      }
    }

    throw new RuntimeException('Unable to generate a unique random code');
  }

  /**
   * {@inheritdoc}
   */
  public function getTicketByCode($code) {
    $tickets = $this->getTicketStorage()->loadByProperties(['code' => $code]);
    return array_pop($tickets);
  }

  /**
   * {@inheritdoc}
   */
  public function getTicketByRedeemer(AccountInterface $account) {
    // TODO: This will have to vary by ticket type, sometime and somehow.
    $tickets = $this->getTicketStorage()->loadByProperties(['redeemer' => $account->id()]);
    return array_pop($tickets);
  }

  /**
   * {@inheritdoc}
   */
  public function getTicketStorage() {
    if (!isset($this->ticketStorage)) {
      $this->ticketStorage = $this->entityTypeManager()->getStorage('dct_commerce_ticket');
    }
    return $this->ticketStorage;
  }

}
