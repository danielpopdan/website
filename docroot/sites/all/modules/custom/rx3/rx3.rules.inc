<?php

/**
 * Implements hook_rules_condition_info().
 */
function rx3_rules_condition_info() {
  return array(
    'rx3_rules_condition_referrer_session' => array(
      'label' => t('Verify if RX3 session parameter exists'),
      'group' => 'RX3',
      'parameter' => array(
        'rx3' => array(
          'type' => 'text',
          'label' => t('RX3 - Referrer'),
        ),
      ),
      'module' => 'rx3',
    ),
    'rx3_rules_condition_referrer_ticket' => array(
      'label' => t('Verify if RX3 ticket parameter exists'),
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
 * Condition: Verify if RX3 parameter exists and it is session.
 */
function rx3_rules_condition_referrer_session($rx3) {
  if (isset($_SESSION["rx3"]) && $_SESSION["rx3"] == 'session') {
    return TRUE;
  }
}

/**
 * Condition: Verify if RX3 parameter exists and it is ticket.
 */
function rx3_rules_condition_referrer_ticket($rx3) {
  if (isset($_SESSION["rx3"]) && $_SESSION["rx3"] == 'ticket') {
    return TRUE;
  }
}