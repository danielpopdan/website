<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections\Repository;

/**
 * Interface AirportConnectionRepositoryInterface.
 *
 * @package Drupal\dct_airport_connections\Repository
 */
interface AirportConnectionRepositoryInterface {

  /**
   * Returns an array of origin titles, mapped by the key.
   *
   * @return array
   *   The list of origins.
   */
  public function getOrigins(): array;

}
