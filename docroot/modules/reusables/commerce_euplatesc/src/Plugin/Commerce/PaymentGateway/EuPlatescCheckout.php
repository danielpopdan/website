<?php

namespace Drupal\commerce_euplatesc\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_euplatesc\Event\EuPlatescEvents;
use Drupal\commerce_euplatesc\Event\EuPlatescPaymentEvent;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\PaymentMethodTypeManager;
use Drupal\commerce_payment\PaymentTypeManager;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\commerce_price\Calculator;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides the EuPlatesc Checkout payment gateway plugin.
 *
 * @CommercePaymentGateway(
 *   id = "euplatesc_checkout",
 *   label = @Translation("EuPlatesc Checkout"),
 *   display_label = @Translation("EuPlatesc"),
 *    forms = {
 *     "offsite-payment" =
 *   "Drupal\commerce_euplatesc\PluginForm\EuPlatescCheckoutForm",
 *   },
 *   payment_method_types = {"credit_card"},
 *   credit_card_types = {
 *     "mastercard", "visa", "maestro",
 *   },
 * )
 */
class EuPlatescCheckout extends OffsitePaymentGatewayBase implements EuPlatescCheckoutInterface {

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructs a new PaymentGatewayBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_payment\PaymentTypeManager $payment_type_manager
   *   The payment type manager.
   * @param \Drupal\commerce_payment\PaymentMethodTypeManager $payment_method_type_manager
   *   The payment method type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, PaymentTypeManager $payment_type_manager, PaymentMethodTypeManager $payment_method_type_manager, TimeInterface $time, EventDispatcherInterface $eventDispatcher) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $payment_type_manager, $payment_method_type_manager, $time);
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.commerce_payment_type'),
      $container->get('plugin.manager.commerce_payment_method_type'),
      $container->get('datetime.time'),
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'merchant_id' => '',
      'secret_key' => '',
      'redirect_method' => 'post',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['merchant_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant ID'),
      '#description' => t('The merchant id from the EuPlatesc.ro provider.'),
      '#default_value' => $this->configuration['merchant_id'],
      '#required' => TRUE,
    ];
    $form['secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret key'),
      '#description' => t('The secret key id from the EuPlatesc.ro provider.'),
      '#default_value' => $this->configuration['secret_key'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['merchant_id'] = $values['merchant_id'];
      $this->configuration['secret_key'] = $values['secret_key'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onReturn(OrderInterface $order, Request $request) {
    $data = $this->getRequestData($request);

    $configuration = $this->getConfiguration();
    $data['fp_hash'] = strtoupper($this->hashData($data, $configuration['secret_key']));
    $fp_hash = addslashes(trim($request->request->get('fp_hash')));

    if ($data['fp_hash'] !== $fp_hash) {
      throw new PaymentGatewayException('Invalid signature');
    }

    $payment = $this->createPaymentStorage($order, $request);

    if ($request->request->get('action') == "0") {
      $order->setData('state', 'completed');
      $payment->state = 'authorization';

      $event = new EuPlatescPaymentEvent($order);
      $this->eventDispatcher->dispatch(EuPlatescEvents::PAYMENT_SUCCESS, $event);

      drupal_set_message(t('The payment was made successfully.'), 'status');

      $order->save();
      $payment->save();
      return TRUE;
    }
    else {
      $payment->state = 'authorization_voided';

      $event = new EuPlatescPaymentEvent($order);
      $this->eventDispatcher->dispatch(EuPlatescEvents::PAYMENT_FAILURE, $event);

      drupal_set_message(t('Transaction failed: @message', ['@message' => $request->request->get['message']]), 'warning');

      $order->save();
      $payment->save();
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() {
    return 'https://secure.euplatesc.ro/tdsprocess/tranzactd.php';
  }

  /**
   * {@inheritdoc}
   */
  public function setEuPlatescCheckoutData(PaymentInterface $payment) {
    $order = $payment->getOrder();

    $amount = $payment->getAmount();
    $configuration = $this->getConfiguration();

    // Order description.
    $order_desc = 'Order #' . $order->id() . ': ';

    foreach ($order->getItems() as $item) {
      $product_sku = $item->getPurchasedEntity()->getSku();
      $order_desc .= $item->getTitle() . ' [' . $product_sku . ']';
      $order_desc .= ', ';
    }

    // Remove the last comma.
    $order_desc = rtrim($order_desc, ', ');

    // Curent timestamp.
    $timestamp = gmdate('YmdHis');
    $nonce = md5(microtime() . mt_rand());

    // Build a name-value pair array for this transaction.
    // The data which should be signed to be transported to EuPlatesc.ro.
    $data = [
      'amount' => Calculator::round($amount->getNumber(), 2),
      'curr' => $amount->getCurrencyCode(),
      'invoice_id' => $order->id(),
      'order_desc' => $order_desc,
      'merch_id' => $configuration['merchant_id'],
      'timestamp' => $timestamp,
      'nonce' => $nonce,
    ];

    $address = $order->getBillingProfile()->get('address')->first();

    // The hidden data wich should be transported to EuPlatesc.ro.
    $nvp_data = [
      'fname' => $address->getGivenName(),
      'lname' => $address->getFamilyName(),
      'country' => $address->getCountryCode(),
      'city' => $address->getLocality(),
      'email' => $order->getEmail(),
      'amount' => Calculator::round($amount->getNumber(), 2),
      'curr' => $amount->getCurrencyCode(),
      'invoice_id' => $order->id(),
      'order_desc' => $order_desc,
      'merch_id' => $configuration['merchant_id'],
      'timestamp' => $timestamp,
      'nonce' => $nonce,
      'fp_hash' => strtoupper($this->hashData($data, $configuration['secret_key'])),
    ];

    return $nvp_data;
  }

  /**
   * Get data from Request object.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The Request object.
   *
   * @return array
   *   Data from the request.
   */
  public function getRequestData(Request $request) {
    return [
      'amount' => addslashes(trim($request->request->get('amount'))),
      'curr' => addslashes(trim($request->request->get('curr'))),
      'invoice_id' => addslashes(trim($request->request->get('invoice_id'))),
      // A unique id provided by EuPlatesc.ro.
      'ep_id' => addslashes(trim($request->request->get('ep_id'))),
      'merch_id' => addslashes(trim($request->request->get('merch_id'))),
      // For the transaction to be ok, the action should be 0.
      'action' => addslashes(trim($request->request->get('action'))),
      // The transaction response message.
      'message' => addslashes(trim($request->request->get('message'))),
      // If the transaction action is different 0, the approval value is empty.
      'approval' => addslashes(trim($request->request->get('approval'))),
      'timestamp' => addslashes(trim($request->request->get('timestamp'))),
      'nonce' => addslashes(trim($request->request->get('nonce'))),
    ];
  }

  /**
   * Create a PaymentStorage object.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The commerce_order object.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The Request object.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The PaymentStorage object,
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function createPaymentStorage(OrderInterface $order, Request $request) {
    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    $request_time = $this->time->getRequestTime();
    return $payment_storage->create([
      'state' => 'authorization',
      'amount' => $order->getTotalPrice(),
      'payment_gateway' => $this->entityId,
      'order_id' => $order->id(),
      'test' => $this->getMode() == 'test',
      'remote_id' => $request->request->get('ep_id'),
      'remote_state' => $request->request->get('message'),
      'authorized' => $request_time,
    ]);
  }

  /**
   * Custom function from EuPlatesc documentation. Read module documentation.
   *
   * @param array $data
   *   Data that is passed through SHA1 function.
   * @param string $key
   *   Secret key.
   *
   * @return string
   *   Hash code that is sent to euplatesc.
   */
  public static function hashData(array $data, $key) {
    $str = NULL;

    foreach ($data as $d) {
      if ($d === NULL || strlen($d) == 0) {
        // The NULL values will be replaced with - .
        $str .= '-';
      }
      else {
        $str .= strlen($d) . $d;
      }
    }

    // We convert the secret code into a binary string.
    $key = pack('H*', $key);

    return self::hashSha1($str, $key);
  }

  /**
   * Custom function from EuPlatesc documentation. Read module documentation.
   *
   * @param string $data
   *   Data regarding the order.
   * @param string $key
   *   Secret key.
   *
   * @return string
   *   The digest of the function.
   */
  private static function hashSha1($data, $key) {
    $blocksize = 64;
    $hashfunc = 'md5';

    if (strlen($key) > $blocksize) {
      $key = pack('H*', $hashfunc($key));
    }

    $key = str_pad($key, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);

    $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
    return bin2hex($hmac);
  }

}
