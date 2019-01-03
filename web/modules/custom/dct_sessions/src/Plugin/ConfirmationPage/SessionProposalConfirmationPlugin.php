<?php

namespace Drupal\dct_sessions\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Url;
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
    return t('Thank you for the proposal!');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('We are glad that you want to share your knowledge at Drupal Developer Days Transylvania! We will take your proposal in consideration and we will keep in touch!');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return [
      'title' => $this->t('Check your proposals'),
      'url' => Url::fromRoute('dct_sessions.user_sessions'),
    ];
  }

}
