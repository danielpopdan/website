<?php

namespace Drupal\dct_airport_connections;

/**
 * Interface DataProviderInterface.
 *
 * @package Drupal\dct_airport_connections
 */
interface DataProviderInterface {

  /**
   * Returns an array containing the plots with the airport connections.
   *
   * @return array
   *   Array containing the plots with the airport connections.
   */
  public function getPlots(): array;

}
