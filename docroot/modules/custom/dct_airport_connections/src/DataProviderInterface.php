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
  public function getPlots();

  /**
   * Returns an array containing the links between origin and the connections.
   *
   * @return array
   *   Array containing the links between the home city and the connections.
   */
  public function getLinks();

}
