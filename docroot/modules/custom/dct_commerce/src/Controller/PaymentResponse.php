<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_payment\PaymentGatewayManager;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaymentResponse.
 *
 * Serves the url used when returning from the payment
 * processor.
 *
 * @package Drupal\dct_commerce\Controller
 */
class PaymentResponse extends ControllerBase {

  /**
   * The payment gateway.
   *
   * @var \Drupal\commerce_euplatesc\Plugin\Commerce\PaymentGateway\EuPlatescCheckout
   */
  private $paymentGateway;

  /**
   * PaymentResponse constructor.
   *
   * @param \Drupal\commerce_payment\PaymentGatewayManager $paymentGatewayManager
   *   The payment gateway plugin manager.
   */
  public function __construct(PaymentGatewayManager $paymentGatewayManager) {
    $configuration = $this->entityTypeManager()->getStorage('commerce_payment_gateway')->load('dct_euplatesc_gateway')->getPluginConfiguration();
    $configuration['_entity_id'] = 'dct_euplatesc_gateway';
    $this->paymentGateway = $paymentGatewayManager->createInstance('euplatesc_checkout', $configuration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('plugin.manager.commerce_payment_gateway')
    );
  }

  /**
   * Constructs a response to the payment processor callback.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   The render array for the response.
   */
  public function content(Request $request) {
    $data = $this->paymentGateway->getRequestData($request);
    $order = Order::load($data['invoice_id']);
    $_SESSION['messages']['extra'] = [];
    if ($order instanceof Order) {
      try {
        $this->paymentGateway->onReturn($order, $request);
      }
      catch (\Exception $e) {
        $_SESSION['messages']['extra'][] = $e->getFile() . ':' . $e->getLine() . $e->getMessage();
      }
    }
    else {
      drupal_set_message($this->t('Invalid request. Please contact the website administrator.'), 'warning');
    }
    $_SESSION['messages']['extra'][] = print_r($request->query->all(), TRUE);
    $_SESSION['messages']['extra'][] = print_r($request->request->all(), TRUE);
    return [
      '#theme' => 'dct_commerce_payment_response',
      '#content' => $_SESSION['messages'],
    ];

  }

}
