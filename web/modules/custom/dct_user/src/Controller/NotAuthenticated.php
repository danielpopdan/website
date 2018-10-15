<?php

namespace Drupal\dct_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Class NotAuthenticated.
 *
 * @package Drupal\dct_user\Controller
 */
class NotAuthenticated extends ControllerBase {

  /**
   * Renders the error page.
   *
   * @return array
   *   The render array.
   */
  public function index() {
    // Sets destination query parameter the current page.
    $query = [];
    if (!empty($_GET['destination'])) {
      $query['query']['destination'] = $_GET['destination'];
    }
    $login = Url::fromRoute('user.login', [], $query)->toString(TRUE)->getGeneratedUrl();
    $register = Url::fromRoute('user.register', [], $query)->toString(TRUE)->getGeneratedUrl();
    return [
      '#theme' => 'dct_user_not_authenticated_error',
      '#links' => [
        'login' => $login,
        'register' => $register,
      ],
    ];
  }

}
