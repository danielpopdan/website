<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\Entity\OrderItemInterface;
use Drupal\Component\Utility\Random;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TicketController.
 */
class TicketController extends ControllerBase implements TicketControllerInterface {

  /**
   * The maximum number of times generateTicketCode() can loop.
   */
  const MAXIMUM_TRIES = 100;

  /**
   * The ticket storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $ticketStorage = NULL;

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManager
   */
  protected $mailManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * TicketController constructor.
   *
   * @param \Drupal\Core\Mail\MailManager $mailManager
   *   The mail manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(MailManager $mailManager, EntityTypeManagerInterface $entity_type_manager) {
    $this->mailManager = $mailManager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('plugin.manager.mail'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function handlePaymentSuccess(OrderInterface $order) {

    // Iterate over the order items.
    foreach ($order->order_items as $item) {
      /* @var $order_item \Drupal\commerce_order\Entity\OrderItemInterface */
      $order_item = $item->entity;
      $quantity = (int) $order_item->getQuantity();
      $tickets = [];

      // Generate one ticket for each item mentioned in the quantity.
      for ($number = 0; $number < $quantity; $number++) {
        $user = $this->currentUser();
        $ticket = $this->createTicket($user, $order_item);
        $ticket->save();

        $tickets[] = $ticket;
      }

      $recipients = $order_item->get('field_recipients')->getValue();
      $params = [
        'code' => NULL,
        'redeem_link' => Url::fromRoute('dct_commerce.ticket_redemption_code')->setAbsolute(TRUE)->toString(TRUE)->getGeneratedUrl(),
      ];

      // Send mails to each of the ticket recipients.
      for ($i = 0; $i < count($tickets); $i++) {
        if (!empty($recipients[$i])) {
          $params['code'] = $tickets[$i]->getCode();
          $this->mailManager->doMail(
            'dct_commerce',
            'ticket_recipient',
            $recipients[$i]['value'],
            $this->currentUser->getPreferredLangcode(),
            $params,
            NULL,
            TRUE
          );
        }
      }
      $params['account'] = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
      // Send mail containing all of the codes
      // to the user who purchased the tickets.
      $params['tickets'] = $tickets;
      $this->mailManager->doMail(
        'dct_commerce',
        'ticket_buyer',
        $this->currentUser->getEmail(),
        $this->currentUser->getPreferredLangcode(),
        $params,
        NULL,
        TRUE
      );

      // Add the tickets to the order item.
      $order_item->set('field_tickets', $tickets);
      $order_item->save();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createTicket(AccountInterface $creator, $orderItem) {

    // TODO: Have this made configurable somewhere.
    $code = $this->generateTicketCode(10, 'DCT2018-');
    $values = [
      'code' => $code,
      'order_item' => $orderItem instanceof OrderItemInterface ? $orderItem->id() : NULL,
      'buyer' => $creator->id(),
    ];

    /* @var $ticket \Drupal\dct_commerce\Entity\TicketInterface */
    $ticket = $this->getTicketStorage()->create($values);

    return $ticket;
  }

  /**
   * {@inheritdoc}
   */
  public function generateTicketCode($codeLength = 10, $prefix = NULL, $suffix = NULL) {
    $random = new Random();

    for ($counter = 0; $counter < static::MAXIMUM_TRIES; $counter++) {
      $code = $prefix . strtoupper($random->name($codeLength)) . $suffix;
      if (empty($this->getTicketByCode($code))) {
        return $code;
      }
    }

    throw new RuntimeException('Unable to generate a unique random code');
  }

  /**
   * {@inheritdoc}
   */
  public function getTicketByCode($code) {
    $tickets = $this->getTicketStorage()->loadByProperties(['code' => $code]);
    return array_pop($tickets);
  }

  /**
   * {@inheritdoc}
   */
  public function getTicketByRedeemer(AccountInterface $account) {
    // TODO: This will have to vary by ticket type, sometime and somehow.
    $tickets = $this->getTicketStorage()->loadByProperties(['redeemer' => $account->id()]);
    return array_pop($tickets);
  }

  /**
   * {@inheritdoc}
   */
  public function getTicketStorage() {
    if (!isset($this->ticketStorage)) {
      $this->ticketStorage = $this->entityTypeManager()->getStorage('dct_commerce_ticket');
    }
    return $this->ticketStorage;
  }

}
