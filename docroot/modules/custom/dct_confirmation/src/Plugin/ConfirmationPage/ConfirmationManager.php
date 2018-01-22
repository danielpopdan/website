<?php

namespace Drupal\dct_confirmation\Plugin\ConfirmationPage;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\dct_confirmation\ConfirmationPageInterface;

/**
 * Class ConfirmationManager.
 *
 * Manages the confirmation page plugins.
 *
 * @package Drupal\dct_confirmation\Plugin\ConfirmationPage
 */
class ConfirmationManager extends DefaultPluginManager {

  /**
   * Constructs a ConfirmationManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ConfirmationPage', $namespaces, $module_handler, 'Drupal\dct_confirmation\ConfirmationPageInterface', 'Drupal\dct_confirmation\Annotation\ConfirmationPage');

    $this->alterInfo('confirmation_page_info');
    $this->setCacheBackend($cache_backend, 'confirmation_page');
  }

  /**
   * Gets the content of the confirmation page.
   *
   * @param string $form_id
   *   The id of the form.
   *
   * @return array|string
   *   The content of the page.
   */
  public function getConfirmationPage($form_id) {
    $pagePlugin = $this->getPagePlugin($form_id);
    if (!empty($pagePlugin)) {
      return $this->getPageContent($pagePlugin);
    }

    return [];
  }

  /**
   * Gets the plugin for the page.
   *
   * @param string $form_id
   *   The id of the form.
   *
   * @return \Drupal\dct_confirmation\ConfirmationPageInterface
   *   The plugin for the page.
   */
  protected function getPagePlugin($form_id) {
    foreach ($this->getDefinitions() as $id => $definition) {
      if ($definition['form_id'] == $form_id) {

        return $this->createInstance($definition['id']);
      }
    }
  }

  /**
   * Builds the render array for a confirmation page.
   *
   * @param \Drupal\dct_confirmation\ConfirmationPageInterface $confirmationPage
   *   The plugin page.
   *
   * @return array
   *   The render array.
   */
  protected function getPageContent(ConfirmationPageInterface $confirmationPage) {
    $build = [];

    $build['#theme'] = 'dct_confirmation_page';

    $build['#title'] = [
      '#markup' => $confirmationPage->getTitle(),
    ];
    $build['#description'] = [
      '#markup' => $confirmationPage->getDescription(),
    ];

    $link = $confirmationPage->getLink();
    $build['#link'] = [
      '#type' => 'link',
      '#title' => $link['title'],
      '#url' => $link['url'],
    ];

    return $build;
  }

}
