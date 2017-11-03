<?php

namespace Drupal\dct_node_access\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Controller\NodeViewController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manages the access for details pages.
 */
class RouteController extends NodeViewController {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $node, $view_mode = 'full', $langcode = NULL) {

    // Get the type of the node.
    $entity = \Drupal::entityTypeManager()->getStorage('node_type')->load($node->bundle());
    $node_type_settings = $entity->getThirdPartySetting('dct_node_access', 'hide_details', 0);
    if (isset($node_type_settings)) {
      if ($node_type_settings === 1) {
        throw new NotFoundHttpException();
      }
    }

    return parent::view($node);
  }

}
