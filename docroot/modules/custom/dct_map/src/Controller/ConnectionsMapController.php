<?php

namespace Drupal\dct_map\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dct_airport_connections\DataProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dct_airport_connections;

/**
 * Class ConnectionsMapController.
 *
 * @package Drupal\dct_map\Controller
 */
class ConnectionsMapController extends ControllerBase {

  /**
   * Data provider the for airport connections.
   *
   * @var \Drupal\dct_airport_connections\DataProviderInterface
   */
  protected $connectionsDataProvider;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dct_airport_connections.data_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(DataProviderInterface $dataProvider) {
    $this->connectionsDataProvider = $dataProvider;
  }

  /**
   * Returns the array associated to the airport connections map.
   *
   * @return array
   */
  public function getMap() {
    return[
      '#theme' => 'dct_map_connections_map',
      '#attached' => [
        'library' => [
          'dct_map/mapael',
          'dct_map/maps_eu',
          'dct_theme/connections-map',
        ],
        'drupalSettings' => [
          'connections-map' => [
            'plots' => $this->connectionsDataProvider->getPlots(),
            'links' => $this->connectionsDataProvider->getLinks(),
          ],
        ],
      ],
    ];
  }

}
