<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections\Repository;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class AirportConnectionRepository.
 *
 * @package Drupal\dct_airport_connections\Repository
 */
class AirportConnectionRepository implements AirportConnectionRepositoryInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * AirportConnectionRepository constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritDoc}
   */
  public function getOrigins(): array {
    $origins = $this->entityTypeManager->getStorage('airport_connection')
      ->loadByProperties(['isOrigin' => TRUE]);
    $result = [];
    foreach ($origins as $origin) {
      $result[$origin->id()] = $origin->label();
    }

    return $result;
  }

}
