<?php

namespace Drupal\invisible_recaptcha;

/**
 * Manages the invisible recaptcha actions.
 */
interface InvisibleRecaptchaInterface {

  /**
   * Validates the recaptcha key.
   *
   * @param string $secretKey
   *   The secret key for server side integration.
   *
   * @return bool
   *   The result after checking if the recaptcha key is valid.
   */
  public function validateInvisibleRecaptchaCode($secretKey);

}
