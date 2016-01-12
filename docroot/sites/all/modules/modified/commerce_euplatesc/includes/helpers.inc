<?php

/**
 * @file helpers.inc
 */

/**
 * Description of helpers
 *
 * @author adrian.mihaila
 */
class CommerceEuPlatescHelpers {

  /**
   * Returns an array of EuPlatesc.ro payment method icon img elements.
   *
   * @return
   *   The array of themed payment method icons keyed by name: visa, mastercard,
   *   maestro, mastercard_securecode, verified_by_visa, euplatesc
   */
  public static function getIcons() {
    $icons = array();

    $payment_methods = array(
      'visa' => t('Visa'),
      'mastercard' => t('Mastercard'),
      'maestro' => t('Maestro'),
      'mastercard_securecode' => t('Mastercard Securecode'),
      'verified_by_visa' => t('Verified by Visa'),
      'euplatesc' => t('EuPlatesc'),
    );

    foreach ($payment_methods as $name => $title) {
      $variables = array(
        'path' => drupal_get_path('module', 'commerce_euplatesc') . '/images/' . $name . '.gif',
        'title' => $title,
        'alt' => $title,
        'attributes' => array(
          'class' => array('commerce-paypal-icon'),
        ),
      );
      $icons[$name] = theme('image', $variables);
    }

    return $icons;
  }

  /**
   * Custom function from EuPlatesc documentation.
   * Fore more details, please read the documentation from module.
   *
   * @param array $data
   * @param string $key
   * @return string.
   */
  public static function hashData($data, $key) {
    $str = NULL;

    foreach ($data as $d) {
      if ($d === NULL || strlen($d) == 0)
        $str .= '-'; // The NULL values will be replaced with -
      else
        $str .= strlen($d) . $d;
    }

    // ================================================================
    $key = pack('H*', $key); // We convert the secret code into a binaty string.
    // ================================================================

    return self::hashSHA1($str, $key);
  }

  /**
   * Custom function from EuPlatesc documentation.
   * Fore more details, please read the documentation from module.
   *
   * @param string $data
   * @param string $key
   * @return string.
   */
  private static function hashSHA1($data, $key) {
    $blocksize = 64;
    $hashfunc = 'md5';

    if (strlen($key) > $blocksize)
      $key = pack('H*', $hashfunc($key));

    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);

    $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
    return bin2hex($hmac);
  }

}
