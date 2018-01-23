<?php

namespace Drupal\dct_commerce\EventSubscriber;

use Drupal\commerce_euplatesc\Event\EuPlatescEvents;
use Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DctCommerceEventSubscriber implements EventSubscriberInterface {

  /**
   * @param \Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent $event
   *   The payment success event.
   */
  public function onPaymentSuccess(EuPlatescPaymentEvent $event) {
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[EuPlatescEvents::PAYMENT_SUCCESS][] = ['onPaymentSuccess'];
    return $events;
  }

}
