<?php

namespace Drupal\invisible_recaptcha;

use Drupal\Core\State\StateInterface;

/**
 * Manages the invisible recaptcha actions.
 */
class InvisibleRecaptcha implements InvisibleRecaptchaInterface {

  /**
   * The api address for checking the invisible recaptcha key.
   *
   * @var string
   */
  const RECAPTCHA_API = 'https://www.google.com/recaptcha/api/siteverify?';

  /**
   * {@inheritdoc}
   */
  public function validateInvisibleRecaptchaCode($secretKey) {
    $recaptcha = $_POST['g-recaptcha-response'];

    $request_data =
      [
        'secret' => $secretKey,
        'remoteip' => \Drupal::request()->getClientIp(),
        'response' => $recaptcha,
      ];

    $url = self::RECAPTCHA_API . http_build_query($request_data);
    $recaptcha_validation = file_get_contents($url);

    // Verifies the response status.
    if (!$recaptcha_validation["succes"]) {
      return FALSE;
    }

    return TRUE;

  }

}
