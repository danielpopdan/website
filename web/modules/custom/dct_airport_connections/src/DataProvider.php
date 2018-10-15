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
  const ORIGIN_NAME = 'Cluj-Napoca';

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
    $connections = $this->entityTypeManager->getStorage('airport_connections')
      ->loadByProperties();

    // Creates associative array containing the locations.
    foreach ($connections as $connection) {
      $plots[$connection->get('title')->first()->value] = [
        'latitude' => $connection->get('latitude')->first()->value,
        'longitude' => $connection->get('longitude')->first()->value,
        'size' => 5,
        'tooltip' => [
          'content' => $connection->get('title')->first()->value,
        ],
        'attrs' => [
          'fill' => '#fcb02a',
        ],
        'attrsHover' => [
          'fill' => '#721139',
        ],
      ];
    }

    $plots[self::ORIGIN_NAME] = [
      'latitude' => self::ORIGIN_LATITUDE,
      'longitude' => self::ORIGIN_LONGITUDE,
      'size' => 10,
      'attrs' => [
        'fill' => '#fcb02a',
      ],
      'attrsHover' => [
        'fill' => '#721139',
      ],
      'tooltip' => [
        'content' => self::ORIGIN_NAME,
      ],
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
          self::ORIGIN_NAME, $title,
        ],
        'tooltip' => [
          'content' => self::ORIGIN_NAME  . ' - ' . $title,
        ]
      ];
    }
    return $links;
  }

}
