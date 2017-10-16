<?php

namespace Drupal\dct_airport_connections;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class DataProvider.
 *
 * @package Drupal\dct_airport_connections
 */
class DataProvider implements DataProviderInterface {

  /**
   * Array containing the latitude and the longitude of the 'home' city. (Cluj)
   *
   * @var array
   */
  protected $home;

  /**
   * The entity_type.manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * DataProvider constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity_type.manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {

    // Sets the latitude of the host city a.k.a. 'home'.
    $this->home = [
      'title' => 'Cluj',
      'latitude' => 46.770439,
      'longitude' => 23.591423,
    ];

    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlots() {
    $plots = [];
    $connections = $this->entityTypeManager->getStorage('airport_connections')->loadByProperties();

    foreach ($connections as $connection) {
      $plots[$connection->get('title')->first()->value] = [
        'latitude' => $connection->get('latitude')->first()->value,
        'longitude' => $connection->get('longitude')->first()->value
      ];
    }

    $plots[$this->home['title']] = [
      'latitude' => $this->home['latitude'],
      'longitude' => $this->home['longitude'],
    ];

    return $plots;
  }

  /**
   * {@inheritdoc}
   */
  public function getLinks() {
    $plots = $this->getPlots();
    $links = [];

    foreach ($plots as $title => $plot) {
      $links[$this->home['title'] . $title] = [
        'between' => [
          [
            'latitude' => $this->home['latitude'],
            'longitude' => $this->home['longitude']
          ],
          [
            'latitude' => floatval($plot['latitude']),
            'longitude' => floatval($plot['longitude'])
          ]
        ],
        // Visual customization of the link.
        'factor' => 0.3,
        'attrs' => [
          'stroke-width' => 2,
        ],
      ];
    }
    return $links;
  }

}
