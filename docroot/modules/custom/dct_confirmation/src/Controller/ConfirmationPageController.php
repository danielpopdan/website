<?php

namespace Drupal\dct_confirmation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dct_confirmation\Plugin\ConfirmationPage\ConfirmationManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ConfirmationPageController.
 *
 * @package Drupal\dct_confirmation\Controller
 */
class ConfirmationPageController extends ControllerBase {

  /**
   * The service that manages the confirmation pages.
   *
   * @var \Drupal\dct_confirmation\Plugin\ConfirmationPage\ConfirmationManager
   */
  protected $confirmationManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfirmationManager $confirmation_manager) {
    $this->confirmationManager = $confirmation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dct_confirmation.plugin.manager.confirmation')
    );
  }

  /**
   * Gets the confirmation page for the form.
   *
   * @param string $form_id
   *   The id of the form.
   *
   * @return array
   *   The render array for the page.
   */
  public function getContent($form_id) {
    return $this->confirmationManager->getConfirmationPage($form_id);
  }

}
