<?php

namespace Drupal\dct_user\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\dct_confirmation\ConfirmationPageInterface;

/**
 * Class UserRegisterConfirmationPlugin.
 *
 * @ConfirmationPage(
 *   id = "user_register_confirmation_plugin",
 *   form_id = "user_register_form"
 * )
 */
class UserRegisterConfirmationPlugin extends PluginBase implements ConfirmationPageInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return t('User Registration Confirmation');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('Registration complete');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return t('link');
  }

}
