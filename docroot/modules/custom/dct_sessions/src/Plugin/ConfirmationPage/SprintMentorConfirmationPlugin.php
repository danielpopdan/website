<?php

namespace Drupal\dct_sessions\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
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
    return t('Become a sprint mentor');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('Become a sprint mentor');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return t('link');
  }

}
