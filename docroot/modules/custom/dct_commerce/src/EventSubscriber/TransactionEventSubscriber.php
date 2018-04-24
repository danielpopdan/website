<?php

namespace Drupal\dct_commerce\EventSubscriber;

use Drupal\commerce_euplatesc\Event\EuPlatescEvents;
use Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent;
use Drupal\dct_bills\InvoiceGenerationServiceInterface;
use Drupal\dct_commerce\Controller\TicketControllerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TransactionEventSubscriber.
 */
class TransactionEventSubscriber implements EventSubscriberInterface {

  /**
   * The ticket controller service.
   *
   * @var \Drupal\dct_commerce\Controller\TicketControllerInterface
   */
  protected $ticketController;

  /**
   * The invoice generation service.
   *
   * @var \Drupal\dct_bills\InvoiceGenerationServiceInterface
   */
  protected $invoiceGeneration;

  /**
   * TransactionEventSubscriber constructor.
   *
   * @param \Drupal\dct_commerce\Controller\TicketControllerInterface $ticketController
   *   The ticket controller.
   * @param \Drupal\dct_bills\InvoiceGenerationServiceInterface $invoice_generation
   *   The invoice generation service.
   */
  public function __construct(TicketControllerInterface $ticketController, InvoiceGenerationServiceInterface $invoice_generation) {
    $this->ticketController = $ticketController;
    $this->invoiceGeneration = $invoice_generation;
  }

  /**
   * Triggers on EuPlatesc payment success.
   *
   * @param \Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent $event
   *   The payment event.
   */
  public function onPaymentSuccess(EuPlatescPaymentEvent $event) {
    $order = $event->getOrder();
    // Set order to completed.
    $order->set('checkout_step', 'complete');
    $order->set('completed', $order->get('changed')->value);
    $order->set('order_number', $order->id());
    $order->set('state', 'completed');
    $order->set('locked', 0);
    $order->setData('state', 'completed');
    $order->save();
    $this->ticketController->handlePaymentSuccess($order);
    $this->invoiceGeneration->generateInvoiceToOrder($order);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[EuPlatescEvents::PAYMENT_SUCCESS][] = ['onPaymentSuccess'];
    return $events;
  }

}
