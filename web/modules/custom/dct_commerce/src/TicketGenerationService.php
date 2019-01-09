<?php

namespace Drupal\dct_commerce;

use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QRCode;
use Drupal\Core\Url;
use Mpdf\Mpdf;
use Drupal\dct_commerce\Entity\TicketInterface;
use Drupal\dct_sessions\Service\UserSessions;

/**
 * Implements invoice generation for orders.
 *
 * @package Drupal\dct_bills
 */
class TicketGenerationService implements TicketGenerationServiceInterface {

  /**
   * {@inheritdoc}
   */
  public function getTicket(TicketInterface $ticket) {
    $theme = 'dct_event_ticket';
    $account = $ticket->getRedeemer();
    $data = Url::fromRoute('dct_commerce.scan_ticket', ['user' => $account->id()]);
    $data->setAbsolute();
    $data = $data->toString();
      $options = new QROptions([
          'version'    => 5,
          'outputType' => QrCode::OUTPUT_MARKUP_SVG,
          'eccLevel'   => QRCode::ECC_L,
      ]);

      // invoke a fresh QRCode instance
      $qr_code = new QRCode($options);
      $shirt_sizes = $account->getFieldDefinition('field_shirt_size')->getSetting('allowed_values');
      $shirt_types = $account->getFieldDefinition('field_gender')->getSetting('allowed_values');
    $render = [
      '#theme' => $theme,
      '#country' => [
        '#markup' => $account->get('field_country')->value,
      ],
      '#given_name' => [
        '#markup' => $account->get('field_first_name')->value,
      ],
      '#family_name' => [
        '#markup' => $account->get('field_last_name')->value,
      ],
      '#username' => [
        '#markup' => $account->get('field_drupal_org_username')->value,
      ],
      '#shirt_size' => [
        '#markup' => $shirt_sizes[$account->get('field_shirt_size')->value],
      ],
      '#shirt_type' => [
        '#markup' => $shirt_types[$account->get('field_gender')->value],
      ],
      '#role' => [
        '#markup' => UserSessions::getPublicUserRoles($account),
      ],
      '#current_date' => [
        '#markup' => date('d.m.Y'),
      ],
      '#qrcode' => $qr_code->render($data),
    ];

    // Attempt to generate the PDF file.
    $pdf = $this->generatePdf($render, $ticket);

    return $pdf;
  }

  /**
   * {@inheritdoc}
   */
  public function generateTicketToUser(TicketInterface $ticket) {
    $account = $ticket->getRedeemer();
    // Retrieves the pdf content.
    $this->getTicket($ticket);
    // Creates the pdf file.
    $content = file_get_contents('public://ticket-order-' . $ticket->id() . '.pdf');
    $file = file_save_data($content, 'public://ticket-order-' . $ticket->id() . '.pdf', FILE_EXISTS_REPLACE);
    $file_usage = \Drupal::service('file.usage');
    $file_usage->add($file, 'dct_tickets', $ticket->getEntityTypeId(), $account->id());
    // Saves the pdf to the order.
    $account->set('field_ticket', ['target_id' => $file->id()]);

    $account->save();

    return $ticket;
  }

    /**
     * Generates a pdf file based on a render array.
     *
     * @param mixed $render
     *   The render array.
     *
     * @return string
     *   The pdf content.
     * @throws \Mpdf\MpdfException
     */
  protected function generatePdf($render, TicketInterface $ticket) {
    $pdf = new Mpdf(['tempDir' => file_directory_temp()]);
    if (in_array(PHP_SAPI, ['cli', 'cli-server', 'phpdbg'])) {
      $html = \Drupal::service('renderer')->renderRoot($render);
    }
    else {
      $html = render($render);
    }
    $orientation = 'portrait';
      $pdf->_setPageSize('A4', $orientation);
      $pdf->WriteHTML(utf8_decode($html));
    $output = $pdf->Output('public://ticket-order-' . $ticket->id() . '.pdf');

    return $output;
  }
}
