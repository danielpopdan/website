<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\commerce_checkout\CheckoutOrderManagerInterface;
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
   * The checkout order manager.
   *
   * @var \Drupal\commerce_checkout\CheckoutOrderManagerInterface
   */
  protected $checkoutOrderManager;

  /**
   * PaymentResponse constructor.
   *
   * @param \Drupal\commerce_payment\PaymentGatewayManager $paymentGatewayManager
   *   The payment gateway plugin manager.
   * @param \Drupal\commerce_checkout\CheckoutOrderManagerInterface $checkout_order_manager
   *   The checkout order manager.
   */
  public function __construct(PaymentGatewayManager $paymentGatewayManager, CheckoutOrderManagerInterface $checkout_order_manager) {
    $configuration = $this->entityTypeManager()->getStorage('commerce_payment_gateway')->load('dct_euplatesc_gateway')->getPluginConfiguration();
    $configuration['_entity_id'] = 'dct_euplatesc_gateway';
    $this->paymentGateway = $paymentGatewayManager->createInstance('euplatesc_checkout', $configuration);
    $this->checkoutOrderManager = $checkout_order_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('plugin.manager.commerce_payment_gateway'),
      $container->get('commerce_checkout.checkout_order_manager'),
      $container->get('logger.factory')
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
      /** @var \Drupal\commerce_checkout\Entity\CheckoutFlowInterface $checkout_flow */
      $checkout_flow = $order->get('checkout_flow')->entity;
      $checkout_flow_plugin = $checkout_flow->getPlugin();
      try {
        $step_id = $this->checkoutOrderManager->getCheckoutStepId($order);
        $this->paymentGateway->onReturn($order, $request);
        $redirect_step_id = $checkout_flow_plugin->getNextStepId($step_id);
        $this->getLogger('commerce_payment')->error("Payment for order " . $order->id() . "successful, redirecting to step " . $redirect_step_id);
        $checkout_flow_plugin->redirectToStep($redirect_step_id);
      }
      catch (\Exception $e) {
        $this->getLogger('commerce_payment')->error($e->getMessage());
        drupal_set_message($this->t('Payment failed at the payment server. Please review your information and try again.'), 'error');
        $redirect_step_id = $checkout_flow_plugin->getPreviousStepId($step_id);
        $checkout_flow_plugin->redirectToStep($redirect_step_id);
      }
    }
    else {
      drupal_set_message($this->t('Invalid request. Please contact the website administrator.'), 'warning');
    }
    return [
      '#theme' => 'dct_commerce_payment_response',
      '#content' => $_SESSION['messages'],
    ];

  }

}