<?php

namespace Drupal\dct_newsletter\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Manages the mailchimp service.
 */
class MailchimpController extends ControllerBase {

  /**
   * Adds an user to the Mailchimp list.
   *
   * @param $user_email
   *   The user email.
   */
  public function addMailchimpUser($user_email, $list_name) {
    $list_id = $this->findListId($list_name);
    if ($list_id) {
      mailchimp_subscribe($list_id, $user_email, NULL, [], FALSE, $format = 'html');
    }
  }

  /**
   * Finds the id of the target list from Mailchimp.
   *
   * @param $list_name
   *   The name of the list.
   *
   * @return null|string
   *   The id of the list, if exists, null otherwise.
   */
  public function findListId($list_name) {
    $mailchimp_lists = mailchimp_get_lists();
    foreach ($mailchimp_lists as $list) {
      if ($list->name == $list_name) {
        return $list->id;
      }
    }

    return NULL;
  }

}
