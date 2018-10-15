<?php

namespace Drupal\dct_map;

/**
 * Interface MapProviderInterface.
 *
 * @package Drupal\dct_map\Controller
 */
interface MapProviderInterface {

  /**
   * Returns the array associated to the airport connections map.
   *
   * @return array
   *   Array associated to the airport connections map.
   */
  public function getMap();

}
