<?php

namespace Drupal\dct_user\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Url;
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
    return t('Thank you for joining us!');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('An email has been sent to your email address to validate your account.');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return [
      'title' => $this->t('Return to homepage'),
      'url' => Url::fromRoute('<front>'),
    ];
  }

}
