<?php

namespace Drupal\dct_bills;

use Dompdf\Dompdf;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\State\StateInterface;

/**
 * Implements invoice generation for orders.
 *
 * @package Drupal\dct_bills
 */
class InvoiceGenerationService implements InvoiceGenerationServiceInterface {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * InvoiceGenerationService constructor.
   *
   * @param \Drupal\Core\Locale\CountryManagerInterface $country_manager
   *   The country manager service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(CountryManagerInterface $country_manager, StateInterface $state) {
    $this->countryManager = $country_manager;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function getInvoice(OrderInterface $order) {
    // Gets the order items.
    $items = $order->getItems();
    $ticket = $items[0];
    // Gets the invoice number to put.
    $invoice_number = $this->getInvoiceNumber();
    // The list of countries is needed to find the country name based on code.
    $list = $this->countryManager->getList();
    $array = explode("\n", wordwrap($ticket->getTitle(), 40));
    $render = [
      '#theme' => 'bill',
      '#country' => [
        '#markup' => $list[$order->getBillingProfile()->get('address')->first()->getCountryCode()],
      ],
      '#city' => [
        '#markup' => $order->getBillingProfile()->get('address')->first()->getLocality(),
      ],
      '#address' => [
        '#markup' => $order->getBillingProfile()->get('address')->first()->getAddressLine1() . ' ' . $order->getBillingProfile()->get('address')->first()->getAddressLine2(),
      ],
      '#given_name' => [
        '#markup' => $order->getBillingProfile()->get('address')->first()->getGivenName(),
      ],
      '#family_name' => [
        '#markup' => $order->getBillingProfile()->get('address')->first()->getFamilyName(),
      ],
      '#phone' => [
        '#markup' => $order->getBillingProfile()->get('field_telephone')->value,
      ],
      '#company' => [
        '#markup' => $order->getBillingProfile()->get('field_company_name')->value,
      ],
      '#tax_no' => [
        '#markup' => $order->getBillingProfile()->get('field_tax_identification_no')->value,
      ],
      '#company_no' => [
        '#markup' => $order->getBillingProfile()->get('field_company_registration_no')->value,
      ],
      '#bank' => [
        '#markup' => $order->getBillingProfile()->get('field_bank_name')->value,
      ],
      '#account_no' => [
        '#markup' => $order->getBillingProfile()->get('field_account_number')->value,
      ],
      '#ticket_title_1' => [
        '#markup' => $array[0],
      ],
      '#ticket_title_2' => [
        '#markup' => $array[1],
      ],
      '#unit_price' => [
        '#markup' => number_format($ticket->getUnitPrice()->getNumber(), 2) . $ticket->getUnitPrice()->getCurrencyCode(),
      ],
      '#quantity' => [
        '#markup' => $ticket->getQuantity(),
      ],
      '#total_item_price' => [
        '#markup' => number_format($ticket->getTotalPrice()->getNumber(), 2) . $ticket->getTotalPrice()->getCurrencyCode(),
      ],
      '#total_price' => [
        '#markup' => number_format($order->getTotalPrice()->getNumber(), 2) . $order->getTotalPrice()->getCurrencyCode(),
      ],
      '#inv_no' => [
        '#markup' => $invoice_number++,
      ],
      '#current_date' => [
        '#markup' => date('d.m.Y', $order->getCompletedTime()),
      ],
    ];
    // Sets the invoice number back, incremented.
    $this->setInvoiceNumber($invoice_number++);

    return $this->generatePdf($render);
  }

  /**
   * {@inheritdoc}
   */
  public function generateInvoiceToOrder(OrderInterface $order) {
    // Retrieves the pdf content.
    $output = $this->getInvoice($order);
    // Creates the pdf file.
    $file = file_save_data($output, 'public://bill-order-' . $order->id() . '.pdf', FILE_EXISTS_REPLACE);
    $file_usage = \Drupal::service('file.usage');
    $file_usage->add($file, 'dct_bills', $order->getEntityTypeId(), $order->id());
    // Saves the pdf to the order.
    $order->set('field_bill', ['target_id' => $file->id()]);

    $order->save();

    return $order;
  }

  /**
   * Generates a pdf file based on a render array.
   *
   * @param mixed $render
   *   The render array.
   *
   * @return string
   *   The pdf content.
   */
  private function generatePdf($render) {
    $dompdf = new Dompdf();
    $html = render($render);
    $dompdf->loadHtml(utf8_decode($html));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    $dompdf->stream();

    return $output;
  }

  /**
   * Retrieves the invoice number.
   *
   * @return mixed|string
   *   The invoice number.
   */
  private function getInvoiceNumber() {
    return (empty($this->state->get('dct_bills.bill_no'))) ? '0' : $this->state->get('dct_bills.bill_no');
  }

  /**
   * Sets the invoice number.
   *
   * @param mixes|string $invoice_number
   *   The invoice number.
   */
  private function setInvoiceNumber($invoice_number) {
    $this->state->set('dct_bills.bill_no', $invoice_number);
  }

}
