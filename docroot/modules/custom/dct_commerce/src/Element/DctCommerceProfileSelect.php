<?php

namespace Drupal\dct_commerce\Element;

use Drupal\commerce_order\Element\ProfileSelect;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom form element for selecting a customer profile.
 *
 * @RenderElement("dct_commerce_profile_select")
 */
class DctCommerceProfileSelect extends ProfileSelect {

  /**
   * {@inheritdoc}
   */
  public static function processForm(array $element, FormStateInterface $form_state, array &$complete_form) {
    $element = parent::processForm($element, $form_state, $complete_form);

    $element['profile_type'] = [
      '#type' => 'radios',
      '#title' => t('Purchase type'),
      '#options' => ['personal' => t('Personal'), 'company' => t('Company')],
      '#default_value' => 'personal',
      '#weight' => -1,
    ];

    $company_visibility_states = [
      'visible' => [
        ':input[name="payment_information[billing_information][profile_type]"]' => ['value' => 'company'],
      ],
      'required' => [
        ':input[name="payment_information[billing_information][profile_type]"]' => ['value' => 'company'],
      ],
    ];

    $company_fields = [
      'field_account_number',
      'field_bank_name',
      'field_company_registration_no',
      'field_tax_identification_no',
    ];

    foreach ($company_fields as $field) {
      $element[$field]['widget'][0]['value']['#states'] = $company_visibility_states;
    }
    $element['field_telephone']['#states']['visible'] = $company_visibility_states['visible'];

    return $element;
  }

}
