<?php

namespace Drupal\dct_airport_connections\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AirportConnectionsSettingsForm
 *
 * @package Drupal\dct_airport_connections\Form
 */
class AirportConnectionsSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'airport_connections_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['airport_connections_settings']['#markup'] = 'Settings form for the airport connections entity. Manage field settings here.';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
