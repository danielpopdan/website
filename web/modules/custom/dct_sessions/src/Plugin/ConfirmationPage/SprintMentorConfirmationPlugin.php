<?php

namespace Drupal\dct_sessions\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Url;
use Drupal\dct_confirmation\ConfirmationPageInterface;

/**
 * Class SprintMentorConfirmationPlugin.
 *
 * @ConfirmationPage(
 *   id = "sprint_mentor_confirmation_plugin",
 *   form_id = "contact_message_sprint_mentor_form_form"
 * )
 */
class SprintMentorConfirmationPlugin extends PluginBase implements ConfirmationPageInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return t('Thank you!');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('We are glad that you want to share your knowledge at DrupalCamp Transylvania! We will take your application in consideration and we will keep in touch!');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return [
      'title' => $this->t('Home'),
      'url' => Url::fromRoute('<front>'),
    ];
  }

}
