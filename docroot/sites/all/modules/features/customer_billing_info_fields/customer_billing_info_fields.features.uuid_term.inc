<?php
/**
 * @file
 * customer_billing_info_fields.features.uuid_term.inc
 */

/**
 * Implements hook_uuid_features_default_terms().
 */
function customer_billing_info_fields_uuid_features_default_terms() {
  $terms = array();

  $terms[] = array(
    'name' => 'Legal entity',
    'description' => '',
    'format' => 'filtered_html',
    'weight' => 0,
    'uuid' => '3c64aeec-26b8-49fd-b339-f7cd9427eaa3',
    'vocabulary_machine_name' => 'person_type',
  );
  $terms[] = array(
    'name' => 'Individual',
    'description' => '',
    'format' => 'filtered_html',
    'weight' => 0,
    'uuid' => 'b0da1089-43de-4239-917c-c6eebb9b9e12',
    'vocabulary_machine_name' => 'person_type',
  );
  return $terms;
}
