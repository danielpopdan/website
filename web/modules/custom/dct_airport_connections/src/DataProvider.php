<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\dct_airport_connections\Entity\AirportConnectionInterface;

/**
 * Class DataProvider.
 *
 * @package Drupal\dct_airport_connections
 */
class DataProvider implements DataProviderInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * DataProvider constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlots(): array {
    $plots = [];
    $links = [];
    $connections = $this->entityTypeManager->getStorage('airport_connection')
      ->loadMultiple();

    // Creates associative array containing the locations.
    foreach ($connections as $connection) {
      $plots[$connection->label()] = $this->formatPlot($connection);
      if (!empty($connection->getOrigin())) {
        $link = $this->formatLink($connection);
        $key = implode('_', $link['between']);
        $links[$key] = $link;
      }
    }

    return [
      'plots' => $plots,
      'links' => $links,
    ];
  }

  /**
   * Formats an airport connection plot.
   *
   * @param \Drupal\dct_airport_connections\Entity\AirportConnectionInterface $airportConnection
   *   The airport connection entity.
   *
   * @return array
   *   The formatted plot.
   */
  private function formatPlot(AirportConnectionInterface $airportConnection) {
    return [
      'latitude' => $airportConnection->getLatitude(),
      'longitude' => $airportConnection->getLongitude(),
      'size' => 5,
      'tooltip' => [
        'content' => $airportConnection->label(),
      ],
      'attrs' => [
        'fill' => '#fcb02a',
      ],
      'attrsHover' => [
        'fill' => '#721139',
      ],
    ];
  }

  /**
   * Formats an airport connection link.
   *
   * @param \Drupal\dct_airport_connections\Entity\AirportConnectionInterface $airportConnection
   *   The airport connection entity.
   *
   * @return array
   *   The formatted link.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function formatLink(AirportConnectionInterface $airportConnection) {
    $origin = $this->entityTypeManager->getStorage('airport_connection')->load(
      $airportConnection->getOrigin()
    );
    return [
      'between' => [
        $origin->label(),
        $airportConnection->label(),
      ],
      'tooltip' => [
        'content' => $origin->label() . ' - ' . $airportConnection->label(),
      ],
    ];
  }

}
