<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Interface TicketControllerInterface.
 */
interface TicketControllerInterface {

  /**
   * Handles the payment success ticket creation.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   */
  public function handlePaymentSuccess(OrderInterface $order);

  /**
   * Creates a new Ticket object.
   *
   * @param \Drupal\Core\Session\AccountInterface $creator
   *   The ticket buyer.
   * @param \Drupal\commerce_order\Entity\OrderItemInterface|null $orderItem
   *   The order item on which the ticket was created.
   *
   * @return \Drupal\dct_commerce\Entity\TicketInterface
   *   The Ticket object.
   */
  public function createTicket(AccountInterface $creator, $orderItem);

  /**
   * Generates a random unique ticket code.
   *
   * @param int $codeLength
   *   The generated code length, without prefix or suffix.
   * @param string|null $prefix
   *   Optional code prefix.
   * @param string|null $suffix
   *   Optional code prefix.
   *
   * @return string
   *   Returns an unique ticket code.
   */
  public function generateTicketCode($codeLength = 8, $prefix = NULL, $suffix = NULL);

  /**
   * Gets the ticket associated with a code.
   *
   * @param string $code
   *   The code string.
   *
   * @return \Drupal\dct_commerce\Entity\TicketInterface|null
   *   The ticket associated with this code, or null if no ticket found.
   */
  public function getTicketByCode($code);

  /**
   * Gets the ticket activated by an user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The ticket redeemer.
   *
   * @return \Drupal\dct_commerce\Entity\TicketInterface|null
   *   The ticket activated with this account, or null if no ticket found.
   */
  public function getTicketByRedeemer(AccountInterface $account);

  /**
   * Gets the ticket storage.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   The entity storage service.
   */
  public function getTicketStorage();

}
