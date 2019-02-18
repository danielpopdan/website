<?php

namespace Drupal\dct_sponsors\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\RouteMatchInterface;

class SponsorsAccessCheck implements AccessInterface
{
    public $route_match;

    /**
     * @param AccountInterface $account
     * @param RouteMatchInterface|null $route_match
     *
     * @return \Drupal\Core\Access\AccessResultAllowed|\Drupal\Core\Access\AccessResultForbidden
     * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
     * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
     */
    public function access(AccountInterface $account, RouteMatchInterface $route_match = null)
    {
        $node = $route_match->getParameter('node');
        if (!empty($node)) {
            if ($node->bundle() == 'sponsor') {
                // The id of the sponsor editor.
                $user_id = $node->get('field_sponsor_editor')->getValue()[0]['target_id'];
                if ($user_id) {
                    if ($account->id() == $user_id) {
                        return AccessResult::allowed();
                    }
                }
            }
        }

        return AccessResult::forbidden();
    }
}
