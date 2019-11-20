<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the airport connection entity.
 *
 * @ContentEntityType(
 *   id = "airport_connection",
 *   label = @Translation("Airport connection"),
 *   handlers = {
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\dct_airport_connections\Form\AirportConnectionForm",
 *       "edit" = "Drupal\dct_airport_connections\Form\AirportConnectionForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "access" = "Drupal\dct_airport_connections\AccessHandler\AirportConnectionAccessControlHandler",
 *   },
 *   base_table = "airport_connection",
 *   admin_permissions = "administer airport_connection entity",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   links = {
 *     "add-form" = "/admin/airport-connection/add",
 *     "edit-form" = "/admin/airport-connection/{airport_connection}/edit",
 *     "delete-form" = "/admin/airport-connection/{airport_connection}/delete",
 *   }
 * )
 */
class AirportConnection extends ContentEntityBase implements AirportConnectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the entity.'))
      ->setReadOnly(TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the entity.'));

    $fields['latitude'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Latitude'))
      ->setDescription(t('The latitude.'));

    $fields['longitude'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Longitude'))
      ->setDescription(t('The longitude'));

    $fields['isOrigin'] = BaseFieldDefinition::create('boolean')
      ->setLabel('isOrigin')
      ->setDescription(t('Specifies if it can be an origin point.'));

    $fields['origin'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Origin ID'))
      ->setDescription(t('The ID of the origin connection.'));

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function label(): ?string {
    return current($this->get('title')->getValue())['value'] ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getLatitude(): ?string {
    return current($this->get('latitude')->getValue())['value'] ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function getLongitude(): ?string {
    return current($this->get('longitude')->getValue())['value'] ?? NULL;
  }

  /**
   * {@inheritDoc}
   */
  public function isOrigin(): bool {
    return !empty(current($this->get('isOrigin')->getValue())['value']);
  }

  /**
   * {@inheritDoc}
   */
  public function getOrigin(): ?int {
    return (int) current($this->get('origin')->getValue())['value'] ?? NULL;
  }

}
