<?php

namespace Drupal\dct_sponsors;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class SponsorServiceProvider extends ServiceProviderBase {
    /**
     * {@inheritdoc}
     */
    public function alter(ContainerBuilder $container) {
        $definition = $container->getDefinition('form_mode_routing.access_checker');
        $definition->setClass('Drupal\dct_sponsors\Access\
        ');
    }
}