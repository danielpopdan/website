<?php

namespace Drupal\dct_bills;

use Drupal\commerce_order\Entity\OrderInterface;

/**
 * Interface InvoiceGenerationServiceInterface.
 *
 * @package Drupal\dct_bills
 */
interface InvoiceGenerationServiceInterface {

  /**
   * Generates the invoice for an order and returns it as a pdf content.
   *
   * The result has to be written to a file.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   *
   * @return mixed
   *   The PDF content.
   */
  public function getInvoice(OrderInterface $order);

  /**
   * Generates and attaches an invoice to an order.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   *
   * @return \Drupal\commerce_order\Entity\OrderInterface
   *   The order.
   */
  public function generateInvoiceToOrder(OrderInterface $order);

}
