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
    return t('We are happy that you are interested in the event and we are eager to see you at DrupalCamp Transylvania.');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return [
      'title' => $this->t('Visit your profile'),
      'url' => Url::fromRoute('user.page'),
    ];
  }

}
