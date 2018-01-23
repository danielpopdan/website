<?php

namespace Drupal\dct_commerce\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Session\AccountInterface;

interface TicketInterface extends ContentEntityInterface {

  /**
   * Gets the ticket code.
   *
   * @return string
   */
  public function getCode();

  /**
   * Gets the order item of this ticket.
   *
   * @return \Drupal\commerce_order\Entity\OrderItemInterface
   */
  public function getOrderItem();

  /**
   * Gets the ticket buyer.
   *
   * @return \Drupal\user\UserInterface
   */
  public function getBuyer();
  /**
   * Checks if this ticket was redeemed.
   *
   * @return bool
   */
  public function isRedeemed();

  /**
   * Activates this ticket for an user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   *
   * @return $this
   *   This Ticket instance.
   */
  public function redeem(AccountInterface $account);

}
