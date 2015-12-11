<?php
/**
 * @file
 * Enables modules and site configuration for a Drupal Camp installation.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function drupalcamp_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // Pre-populate the site name from default config. This is a bit twisted
  // but unfortunately the only way to make this persist through the form.
  $form['site_information']['site_name']['#default_value'] = \Drupal::config('system.site')->get('name');
}
