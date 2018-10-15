<?php

namespace Drupal\dct_user\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Url;
use Drupal\dct_confirmation\ConfirmationPageInterface;

/**
 * ForgotPasswordConfirmationPlugin.
 *
 * @ConfirmationPage(
 *   id = "forgot_password_confirmation_plugin",
 *   form_id = "user_pass"
 * )
 */
class ForgotPasswordConfirmationPlugin extends PluginBase implements ConfirmationPageInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return t('Password recovery');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('Password recovery was successful,  one time login link was sent to your email address.');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return [
      'title' => $this->t('Return to hompage'),
      'url' => Url::fromRoute('<front>'),
    ];
  }

}
