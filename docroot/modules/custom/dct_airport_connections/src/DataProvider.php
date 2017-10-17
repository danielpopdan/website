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
   * Latitude of the origin city.
   */
  const ORIGIN_LATITUDE = 46.770439;

  /**
   * Longitude of the origin city.
   */
  const ORIGIN_LONGITUDE = 23.591423;

  /**
   * Name of the origin city.
   */
  const ORIGIN_NAME = 'Cluj';

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
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlots() {
    $plots = [];
    $connections = $this->entityTypeManager->getStorage('airport_connections')->loadByProperties();

    // Creates associative array containing the locations.
    foreach ($connections as $connection) {
      $plots[$connection->get('title')->first()->value] = [
        'latitude' => $connection->get('latitude')->first()->value,
        'longitude' => $connection->get('longitude')->first()->value
      ];
    }

    $plots[self::ORIGIN_NAME] = [
      'latitude' => self::ORIGIN_LATITUDE,
      'longitude' => self::ORIGIN_LONGITUDE,
    ];

    return $plots;
  }

  /**
   * {@inheritdoc}
   */
  public function getLinks() {
    $plots = $this->getPlots();
    $links = [];

    // Creates associative array containing the links between locations.
    foreach ($plots as $title => $plot) {
      $links[self::ORIGIN_NAME . $title] = [
        'between' => [
          [
            'latitude' => self::ORIGIN_LATITUDE,
            'longitude' => self::ORIGIN_LONGITUDE,
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
