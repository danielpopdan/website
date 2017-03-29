<?php

/**
 * @file hooks.inc
 */

/**
 * Description of hooks
 *
 * @author adrian.mihaila
 */
class CommerceEuPlatescHooks {

  /**
   * Implements hook_menu()
   */
  public static function menu() {
    $items = array();

    $items['checkout/%/redirect'] = array(
      'page callback' => 'checkout_redirect_payment',
      'page arguments' => array(1),
      'access arguments' => array('access content'),
      'file' => 'pages/checkout.inc',
      'type' => MENU_CALLBACK,
    );

    $items['checkout/response'] = array(
      'page callback' => 'checkout_response_payment',
      'access arguments' => array('access content'),
      'file' => 'pages/checkout.inc',
      'type' => MENU_CALLBACK,
    );
//https://secure.euplatesc.ro/tdsprocess/replyd.php
    return $items;
  }

  /**
   * Implements hook_commerce_payment_method_info().
   */
  public static function commercePaymentMethodInfo() {
    $payment_methods = array();

    $icons = CommerceEuPlatescHelpers::getIcons();
    $display_title = t('EuPlatesc.ro - Evolved Ecommerce', array('!logo' => $icons['euplatesc']));
    $display_title .= '<div class="commerce-paypal-icons">' . implode(' ', $icons) . '</div>';

    $payment_methods['euplatesc'] = array(
      'base' => 'commerce_euplatesc',
      'title' => t('EuPlatesc.ro'),
      'short_title' => t('EuPlatesc'),
      'display_title' => $display_title,
      'description' => t('EuPlatesc Payments Standard'),
      'terminal' => FALSE,
      'offsite' => TRUE,
      'offsite_autoredirect' => TRUE,
    );

    return $payment_methods;
  }

  /**
   * Payment method callback: settings form.
   */
  public static function settingsForm($settings) {
    $form = array();

    $form['merch_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Merchant ID'),
      '#description' => t('The merchant id from the EuPlatesc.ro provider.'),
      '#default_value' => $settings['merch_id'],
      '#required' => TRUE,
    );

    $form['key'] = array(
      '#type' => 'textfield',
      '#title' => t('Secret key'),
      '#description' => t('The secret key id from the EuPlatesc.ro provider.'),
      '#default_value' => $settings['key'],
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * Payment method callback: submit form.
   *
   * Add a css to the submission form.
   */
  public static function submitForm($payment_method, $pane_values, $checkout_pane, $order) {
    $form = array();

    $form['description'] = array(
      '#type' => 'markup',
      '#markup' => t('Before you fill in the payment information, please check if your card is activated for online payments.')
    );

    $form['#attached']['css'] = array(
      drupal_get_path('module', 'commerce_euplatesc') . '/css/commerce_euplatesc.css',
    );

    return $form;
  }

  /**
   * Payment method callback: redirect form.
   */
  public static function redirectForm($form, &$form_state, $order, $payment_method) {
    $settings = array(
      // Return to the previous page when payment is canceled
      'cancel_return' => url('checkout/' . $order->order_id . '/payment/back/' . $order->data['payment_redirect_key'], array('absolute' => TRUE)),
      // Return to the payment redirect page for processing successful payments
      'return' => url('checkout/' . $order->order_id . '/payment/return/' . $order->data['payment_redirect_key'], array('absolute' => TRUE)),
      // Specify the current payment method instance ID in the notify_url
      'payment_method' => $payment_method['instance_id'],
    );

    module_load_include('inc', 'commerce_euplatesc', 'pages/checkout');
    return commerce_euplatesc_order_form($form, $form_state, $order, $payment_method['settings'] + $settings);
  }

}
