<?php

namespace Drupal\dct_node_access\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Manages the controllers for the node types.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('entity.node.canonical')) {
      $route->setDefaults([
        '_controller' => '\Drupal\dct_node_access\Controller\RouteController::view',
      ]);
      $route->setRequirements(['_permission' => 'access content']);
    }
  }

}
