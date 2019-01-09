<?php

namespace Drupal\dct_commerce;

use Drupal\dct_commerce\Entity\TicketInterface;

/**
 * Interface InvoiceGenerationServiceInterface.
 *
 * @package Drupal\dct_bills
 */
interface TicketGenerationServiceInterface {

  /**
   * Generates the ticket for a user.
   *
   * The result has to be written to a file.
   *
   * @param \Drupal\dct_commerce\Entity\TicketInterface
   *   The user.
   *
   * @return mixed
   *   The PDF content.
   */
  public function getTicket(TicketInterface $ticket);

  /**
   * Generates and attaches an invoice to an order.
   *
   * @param \Drupal\dct_commerce\Entity\TicketInterface
   *   The user.
   *
   * @return \Drupal\dct_commerce\Entity\TicketInterface
   *   The user.
   */
  public function generateTicketToUser(TicketInterface $ticket);

}
