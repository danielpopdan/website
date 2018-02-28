<?php

namespace Drupal\dct_schedule;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\user\UserInterface;

/**
 * Class ScheduleService.
 *
 * @package Drupal\dct_schedule
 */
class ScheduleProvider implements ScheduleProviderInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * ScheduleService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getSchedule($day, UserInterface $user = NULL) {
    // Gets the session sorted by starting hour, based on the day.
    $query = $this->entityTypeManager->getStorage('node')
      ->getAggregateQuery()
      ->condition('type', ['session', 'breaks_social_activities'], 'IN')
      ->condition('status', 1)
      ->condition('field_day', $day['id'])
      ->sort('field_hour')
      ->sort('field_room')
      ->sort('nid')
      ->sort('type');

    $results = $query->execute();
    usort($results, [$this, 'cmp']);

    $schedule = [];
    // Set the order for the rooms.
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadTree('rooms');
    foreach ($terms as $term) {
      $schedule[$term->tid] = [];
    }
    // Group the results by the room.
    foreach ($results as $result) {
      if ($result['type'] == 'breaks_social_activities' && empty($result['field_room_target_id'])) {
        foreach ($terms as $term) {
          $schedule[$term->tid][] = $result['nid'];
        }
      }
      else {
        $schedule[$result['field_room_target_id']][] = $result['nid'];
      }
    }

    return $schedule;
  }

  /**
   * Sorting helper for schedule.
   *
   * @param int $a
   *   The first value to compare.
   * @param int $b
   *   The second value to compare.
   *
   * @return int
   *   The result.
   */
  private function cmp($a, $b) {
    if ($a['field_hour_value'] == $b['field_hour_value']) {
      return 0;
    }
    return ($a['field_hour_value'] < $b['field_hour_value']) ? -1 : 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getConferenceDays() {
    $days = [];
    // Load the conference days, based on the order set on taxonomies.
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadTree('conference_days');
    foreach ($terms as $term) {
      $days[] = [
        'id' => $term->tid,
        'name' => $term->name,
      ];
    }

    return $days;
  }

}
