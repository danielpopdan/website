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
    $array = explode("\n", wordwrap($ticket->getTitle(), 28));
    // Check if there is any promotion.
    $stores = $ticket->getPurchasedEntity()->getStores();
    $store = array_pop($stores);
    $final_price = \Drupal::service('dct_commerce.promotional_price_calculator')->calculatePromotionalPrice($ticket->getPurchasedEntity(), $store);
    // Increments the invoice number.
    $invoice_number++;

    $profile = $order->getBillingProfile();

    /* @var $address \CommerceGuys\Addressing\AddressInterface */
    $address = $profile->address->first();
    $theme = 'bill';

    // If not a company, use a personal bill.
    if (empty($profile->field_company_name->value) && empty($profile->field_company_registration_no->value)) {
      $theme = 'bill-personal';
    }

    $render = [
      '#theme' => $theme,
      '#country' => [
        '#markup' => $list[$address->getCountryCode()],
      ],
      '#city' => [
        '#markup' => $address->getLocality(),
      ],
      '#address' => [
        '#markup' => $address->getAddressLine1() . ' ' . $address->getAddressLine2(),
      ],
      '#given_name' => [
        '#markup' => $address->getGivenName(),
      ],
      '#family_name' => [
        '#markup' => $address->getFamilyName(),
      ],
      '#phone' => [
        '#markup' => $profile->get('field_telephone')->value,
      ],
      '#company' => [
        '#markup' => $profile->get('field_company_name')->value,
      ],
      '#tax_no' => [
        '#markup' => $profile->get('field_tax_identification_no')->value,
      ],
      '#company_no' => [
        '#markup' => $profile->get('field_company_registration_no')->value,
      ],
      '#bank' => [
        '#markup' => $profile->get('field_bank_name')->value,
      ],
      '#account_no' => [
        '#markup' => $profile->get('field_account_number')->value,
      ],
      '#county' => [
        '#markup' => $profile->get('field_county')->value,
      ],
      '#ticket_title_1' => [
        '#markup' => $array[0],
      ],
      '#ticket_title_2' => [
        '#markup' => $array[1],
      ],
      '#ticket_title_3' => [
        '#markup' => $array[2],
      ],
      '#unit_price' => [
        '#markup' => number_format($order->getTotalPrice()
              ->getNumber() / $ticket->getQuantity(), 2) . $final_price['price']->getCurrencyCode(),
      ],
      '#quantity' => [
        '#markup' => $ticket->getQuantity(),
      ],
      '#total_item_price' => [
        '#markup' => number_format($order->getTotalPrice()->getNumber(), 2) . $final_price['price']->getCurrencyCode(),
      ],
      '#total_price' => [
        '#markup' => number_format($order->getTotalPrice()->getNumber(), 2) . $order->getTotalPrice()->getCurrencyCode(),
      ],
      '#inv_no' => [
        '#markup' => $invoice_number,
      ],
      '#current_date' => [
        '#markup' => date('d.m.Y', $order->get('completed')->value),
      ],
    ];

    // Attempt to generate the PDF file.
    $pdf = $this->generatePdf($render);

    // Sets the invoice number back, incremented.
    $this->setInvoiceNumber($invoice_number);

    return $pdf;
  }

  /**
   * {@inheritdoc}
   */
  public function generateInvoiceToOrder(OrderInterface $order) {
    // Retrieves the pdf content.
    $output = $this->getInvoice($order);
    // Creates the pdf file.
    $file = file_save_data($output, 'public://invoice-order-' . $order->id() . '.pdf', FILE_EXISTS_REPLACE);
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
  protected function generatePdf($render) {
    $dompdf = new Dompdf();
    if (in_array(PHP_SAPI, ['cli', 'cli-server', 'phpdbg'])) {
      $html = \Drupal::service('renderer')->renderRoot($render);
    }
    else {
      $html = render($render);
    }
    $dompdf->loadHtml(utf8_decode($html));
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();

    return $output;
  }

  /**
   * Retrieves the invoice number.
   *
   * @return mixed|string
   *   The invoice number.
   */
  protected function getInvoiceNumber() {
    return (empty($this->state->get('dct_bills.bill_no'))) ? '0' : $this->state->get('dct_bills.bill_no');
  }

  /**
   * Sets the invoice number.
   *
   * @param mixed|string $invoice_number
   *   The invoice number.
   */
  protected function setInvoiceNumber($invoice_number) {
    $this->state->set('dct_bills.bill_no', $invoice_number);
  }

}
