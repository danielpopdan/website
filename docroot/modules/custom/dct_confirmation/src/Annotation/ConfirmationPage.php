<?php

namespace Drupal\dct_confirmation\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Class ConfirmationPage.
 *
 * Annotation for plugin that manages confirmation pages.
 *
 * @package Drupal\dct_confirmation\Annotation
 *
 * @Annotation
 */
class ConfirmationPage extends Plugin {

  /**
   * The id of the plugin.
   *
   * @var string
   */
  public $id;

  /**
   * The id of the form.
   *
   * @var string
   */
  public $formId;

}
