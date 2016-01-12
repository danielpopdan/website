<?php

define('COMMERCE_ORDER_STATUS_COMPLETED', 'completed');
define('COMMERCE_ORDER_STATUS_CANCELED', 'canceled');
define('COMMERCE_ORDER_STATUS_PENDING', 'pending');

/**
 * Custom callback function which return the order form.
 *
 * @return array.
 */
function commerce_euplatesc_order_form($form, &$form_state, $order, $settings) {
  $order_wrapper = entity_metadata_wrapper('commerce_order', $order);

  // Order description.
  $order_desc = '';

  // Get all products.
  $products = array();
  foreach ($order_wrapper->commerce_line_items as $delta => $line_item_wrapper) {
    if (in_array($line_item_wrapper->type->value(), commerce_product_line_item_types())) {
      $product = commerce_product_load_by_sku($line_item_wrapper->commerce_product->sku->value());
      $order_desc .= $product->title . ' [' . $product->sku . ']';
      $order_desc .= ', ';

      $products[] = $product;
    }
  }

  // Remove the last comma.
  $order_desc = rtrim($order_desc, ', ');

  // Get the customer profile.
  $profile = commerce_customer_profile_load($order_wrapper->commerce_customer_billing->profile_id->value());
  $profile_wrapper = entity_metadata_wrapper('commerce_customer_profile', $profile);

  $customer_name_line = explode(' ', $profile_wrapper->field_address->name_line->value());
  $customer_first_name = $profile_wrapper->field_address->first_name->value() ? $profile_wrapper->field_address->first_name->value() : $customer_name_line[0];
  array_shift($customer_name_line);
  $customer_last_name = $profile_wrapper->field_address->last_name->value() ? $profile_wrapper->field_address->last_name->value() : implode(' ', $customer_name_line);

  // Curent timestamp.
  $timestamp = gmdate('YmdHis');
  $nonce = md5(microtime() . mt_rand());

  // The data which should be signed to be transported to EuPlatesc.ro.
  $data = array(
    'amount' => commerce_currency_amount_to_decimal($order_wrapper->commerce_order_total->amount->value()),
    'curr' => $order_wrapper->commerce_order_total->currency_code->value(),
    'invoice_id' => $order->order_id,
    'order_desc' => $order_desc,
    'merch_id' => $settings['merch_id'],
    'timestamp' => $timestamp,
    'nonce' => $nonce,
  );

  // The hidden data wich should be transported to EuPlatesc.ro.
  $hidden = array(
    'fname' => $customer_first_name,
    'lname' => $customer_last_name,
    'country' => $profile_wrapper->field_address->country->value(),
    'city' => $profile_wrapper->field_address->locality->value(),
    'email' => $order->mail,
    'amount' => commerce_currency_amount_to_decimal($order_wrapper->commerce_order_total->amount->value()),
    'curr' => $order_wrapper->commerce_order_total->currency_code->value(),
    'invoice_id' => $order->order_id,
    'order_desc' => $order_desc,
    'merch_id' => $settings['merch_id'],
    'timestamp' => $timestamp,
    'nonce' => $nonce,
    'fp_hash' => strtoupper(CommerceEuPlatescHelpers::hashData($data, $settings['key']))
  );

  $form['#action'] = 'https://secure.euplatesc.ro/tdsprocess/tranzactd.php';

  foreach ($hidden as $name => $value) {
    if (!empty($value)) {
      $form[$name] = array('#type' => 'hidden', '#value' => $value);
    }
  }

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Proceed to EuPlatesc.ro'),
  );

  return $form;
}

/**
 * Custom callback function which update the transaction.
 */
function checkout_response_payment() {
  $data = array(
    'amount' => addslashes(trim(@$_POST['amount'])),
    'curr' => addslashes(trim(@$_POST['curr'])),
    'invoice_id' => addslashes(trim(@$_POST['invoice_id'])),
    'ep_id' => addslashes(trim(@$_POST['ep_id'])), // An unique id provided by EuPlatesc.ro.
    'merch_id' => addslashes(trim(@$_POST['merch_id'])),
    'action' => addslashes(trim(@$_POST['action'])), // For the transaction to be ok, the action should be 0.
    'message' => addslashes(trim(@$_POST['message'])), // The transaction response message.
    'approval' => addslashes(trim(@$_POST['approval'])), // If the transaction action is different 0, the approval value is empty.
    'timestamp' => addslashes(trim(@$_POST['timestamp'])),
    'nonce' => addslashes(trim(@$_POST['nonce'])),
  );

  // Load the order.
  $order = commerce_order_load($data['invoice_id']);
  $order_wrapper = entity_metadata_wrapper('commerce_order', $order);

  // Load the payment method.
  $payment_method = commerce_payment_method_instance_load($order->data['payment_method']);

  $data['fp_hash'] = strtoupper(CommerceEuPlatescHelpers::hashData($data, $payment_method['settings']['key']));
  $fp_hash = addslashes(trim(@$_POST['fp_hash']));

  if ($data['fp_hash'] === $fp_hash) {
    $transaction = commerce_payment_transaction_new('commerce_euplatesc', $order->order_id);
    $transaction->instance_id = $payment_method['instance_id'];
    $transaction->remote_id = $data['ep_id'];
    $transaction->amount = $order_wrapper->commerce_order_total->amount->value();
    $transaction->currency_code = $data['curr'];
    $transaction->message = $data['message'] . ' @variables';

    if ($data['action'] == "0") {
      $order->status = COMMERCE_ORDER_STATUS_COMPLETED;

      $transaction->status = COMMERCE_PAYMENT_STATUS_SUCCESS;
      $transaction->message_variables = array('@variables' => $data['approval']);

      drupal_set_message(t('The payment was made successfully.'), 'status');
    }
    else {
      $transaction->status = COMMERCE_PAYMENT_STATUS_FAILURE;

      drupal_set_message(t('Transaction failed: @message', array('@message' => $data['message'])), 'warning');
    }

    commerce_order_save($order);
    commerce_payment_transaction_save($transaction);
  }
  else {
    watchdog('commerce_euplatesc', t('Invalid signature: !variables', array('!variables' => json_encode($_POST))));
  }

  drupal_goto('<front>');
  drupal_flush_all_caches();
}
