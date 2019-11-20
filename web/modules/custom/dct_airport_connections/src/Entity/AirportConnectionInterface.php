<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Implements entity methods.
 *
 * @package Drupal\dct_airport_connections\Entity
 */
interface AirportConnectionInterface extends ContentEntityInterface {

  /**
   * Returns the latitude.
   *
   * @return string|null
   *   The latitude.
   */
  public function getLatitude(): ?string;

  /**
   * Returns the longitude.
   *
   * @return string|null
   *   The longitude.
   */
  public function getLongitude(): ?string;

  /**
   * Checks if it is in an origin.
   *
   * @return bool
   *   True if it is an origin, false otherwise.
   */
  public function isOrigin(): bool;

  /**
   * Returns the origin.
   *
   * @return int|null
   *   The origin.
   */
  public function getOrigin(): ?int;

}
