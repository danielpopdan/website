<?php

namespace Drupal\dct_map;

use Drupal\dct_airport_connections\DataProviderInterface;

/**
 * Class MapProvider.
 *
 * @package Drupal\dct_map\Controller
 */
class MapProvider implements MapProviderInterface {

  /**
   * Data provider the for airport connections.
   *
   * @var \Drupal\dct_airport_connections\DataProviderInterface
   */
  protected $connectionsDataProvider;

  /**
   * {@inheritdoc}
   */
  public function __construct(DataProviderInterface $dataProvider) {
    $this->connectionsDataProvider = $dataProvider;
  }

  /**
   * {@inheritdoc}
   */
  public function getMap() {
    return [
      '#theme' => 'dct_map_connections_map',
      '#attached' => [
        'library' => [
          'dct_map/mapael',
          'dct_map/maps_eu',
          'dct_map/connections-map',
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
