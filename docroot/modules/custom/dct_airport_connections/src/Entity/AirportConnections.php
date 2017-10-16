<?php

namespace Drupal\dct_airport_connections\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the airport connections entity.
 *
 * @ContentEntityType(
 *   id = "airport_connections",
 *   label = @Translation("Airport connections entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dct_airport_connections\AirportConnectionsListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\dct_airport_connections\Form\AirportConnectionsForm",
 *       "edit" = "Drupal\dct_airport_connections\Form\AirportConnectionsForm",
 *       "delete" = "Drupal\dct_airport_connections\Form\AirportConnectionsDeleteForm",
 *     },
 *     "access" = "Drupal\dct_airport_connections\AirportConnectionsAccessControlHandler",
 *   },
 *   base_table = "airport_connections",
 *   admin_permissions = "administer airport_connections entity",
 *   entity_keys = {
 *     "id" = "id"
 *   },
 *   links = {
 *     "add-form" = "/admin/airport-connections/add",
 *     "edit-form" = "/admin/airport-connections/{airport_connections}/edit",
 *     "delete-form" = "/admin/airport-connections/{airport_connections}/delete",
 *     "collection" = "/admin/airport-connections/list"
 *   },
 *   field_ui_base_route = "airport_connections.settings",
 * )
 */
class AirportConnections extends ContentEntityBase implements AirportConnectionsInterface {

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations() {
    $changed = $this->getUntranslated()->getChangedTime();
    foreach ($this->getTranslationLanguages(FALSE) as $language) {
      $translation_changed = $this->getTranslation($language->getId())->getChangedTime();
      $changed = max($translation_changed, $changed);
    }
    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
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

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of the entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
