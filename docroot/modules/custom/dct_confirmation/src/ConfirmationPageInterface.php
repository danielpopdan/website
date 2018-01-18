<?php

namespace Drupal\dct_confirmation;

/**
 * Interface ConfirmationPageInterface.
 *
 * @package Drupal\dct_confirmation
 */
interface ConfirmationPageInterface {

  /**
   * Get the title of the confirmation page.
   *
   * @return string
   *   The title of the confirmation page.
   */
  public function getTitle();

  /**
   * Get the description of the confirmation page.
   *
   * @return string
   *   The description of the confirmation page.
   */
  public function getDescription();

  /**
   * Get the button link of the confirmation page.
   *
   * @return string
   *   The link of the confirmation page.
   */
  public function getLink();

}
