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

    $company_fields = [
      'field_company_name',
      'field_tax_identification_no',
      'field_company_registration_no',
      'field_bank_name',
      'field_account_number',
      'field_county',
      'field_telephone'
    ];

    $company_visibility_states = [
      'visible' => [
        ':input[name="payment_information[billing_information][profile_type]"]' => ['value' => 'company'],
      ],
      'required' => [
        ':input[name="payment_information[billing_information][profile_type]"]' => ['value' => 'company'],
      ],
    ];

    // Set wrappers to the fields.
    $element['company'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'form-two-column',
        ]
      ],
      '#states' => $company_visibility_states,
    ];

    foreach ($company_fields as $weight => $field) {
      $element[$field]['widget'][0]['value']['#states'] = $company_visibility_states;
      $element['company'][$field] = $element[$field];
      $element['company'][$field]['#weight'] = $weight;

      unset($element[$field]);
    }

    return $element;
  }

}
