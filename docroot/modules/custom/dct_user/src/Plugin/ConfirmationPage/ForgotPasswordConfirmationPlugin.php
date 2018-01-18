<?php

namespace Drupal\dct_user\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\dct_confirmation\ConfirmationPageInterface;

/**
 * ForgotPasswordConfirmationPlugin.
 *
 * @ConfirmationPage(
 *   id = "forgot_password_confirmation_plugin",
 *   form_id = "user_forgot_password_form"
 * )
 */
class ForgotPasswordConfirmationPlugin extends PluginBase implements ConfirmationPageInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return t('Forgot password confirmation');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('Reset password');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return t('link');
  }

}
