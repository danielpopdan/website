<?php

namespace Drupal\dct_airport_connections;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Class AirportConnectionsListBuilder
 *
 * @package Drupal\dct_airport_connections
 */
class AirportConnectionsListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('CID');
    $header['title'] = $this->t('Title');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['id'] = $entity->id();
    $row['title'] = $entity->get('title')->first()->getValue()['value'];

    return $row + parent::buildRow($entity);
  }

}
