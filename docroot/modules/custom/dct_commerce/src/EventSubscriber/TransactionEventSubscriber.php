<?php

namespace Drupal\dct_commerce\EventSubscriber;

use Drupal\commerce_euplatesc\Event\EuPlatescEvents;
use Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\dct_commerce\Controller\TicketControllerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TransactionEventSubscriber implements EventSubscriberInterface {

  /**
   * The ticket controller service.
   *
   * @var \Drupal\dct_commerce\Controller\TicketControllerInterface
   */
  protected $ticketController;

  public function __construct(TicketControllerInterface $ticketController) {
    $this->ticketController = $ticketController;
  }

  /**
   *  Triggers on EuPlatesc payment success.
   *
   * @param \Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent $event
   *   The payment event.
   */
  public function onPaymentSuccess(EuPlatescPaymentEvent $event) {
    $order = $event->getOrder();
    $this->ticketController->handlePaymentSuccess($order);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[EuPlatescEvents::PAYMENT_SUCCESS][] = ['onPaymentSuccess'];
    return $events;
  }

}
