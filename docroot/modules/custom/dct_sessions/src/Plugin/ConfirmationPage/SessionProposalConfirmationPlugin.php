<?php

namespace Drupal\dct_sessions\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\dct_confirmation\ConfirmationPageInterface;

/**
 * Class SessionProposalConfirmationPlugin.
 *
 * @ConfirmationPage(
 *   id = "session_proposal_confirmation_plugin",
 *   form_id = "contact_message_session_proposal_form_form"
 * )
 */
class SessionProposalConfirmationPlugin extends PluginBase implements ConfirmationPageInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return t('Session proposal');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('Session proposal');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return t('link');
  }

}
