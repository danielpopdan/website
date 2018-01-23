<?php

namespace Drupal\commerce_euplatesc\Event;

final class EuPlatescEvents {

  /**
   * Name of the event fired when EuPlatesc authorizes a transaction.
   *
   * @Event
   *
   * @see \Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent
   */
  const PAYMENT_SUCCESS = 'commerce_euplatesc.payment_success';

  /**
   * Name of the event fired when EuPlatesc voids a transaction.
   *
   * @Event
   *
   * @see \Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent
   */
  const PAYMENT_FAILURE = 'commerce_euplatesc.payment_failure';

}
