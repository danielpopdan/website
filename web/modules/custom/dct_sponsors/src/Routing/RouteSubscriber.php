<?php

namespace Drupal\dct_sponsors\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection) {
              // Always deny access to '/user/logout'.
        // Note that the second parameter of setRequirement() is a string.
//        var_dump($collection->get('form_mode_routing.node.sponsor_edit')); die;
        if ($route = $collection->get('form_mode_routing.node.sponsor_edit')) {
            $route->setRequirement('_custom_access', 'Drupal\dct_sponsors\Access\SponsorsAccessCheck::access');
        }
    }

}