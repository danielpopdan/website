<?php

/**
 * Implements hook_rules_condition_info().
 */
function rx3_rules_condition_info() {
  return array(
    'rx3_rules_condition_referrer' => array(
      'label' => t('Verify if RX3 parameter exists'),
      'group' => 'RX3',
      'parameter' => array(
        'rx3' => array(
          'type' => 'text',
          'label' => t('RX3 - Referrer'),
        ),
      ),
      'module' => 'rx3',
    ),
  );
}

/**
 * Condition: Verify if RX3 parameter exists.
 */
function rx3_rules_condition_referrer($rx3) {
//  error_log(print_r($_SESSION, true));
//  error_log('test : ' . $_SESSION['rx3']);
//  if ($rx3 == 'session') {
//    return TRUE;
//  }
  dpm($rx3);
  if (isset($_SESSION["rx3"]) && $_SESSION["rx3"] == 'session') {
    return TRUE;
  }
}