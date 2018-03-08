<?php

namespace Drupal\dct_schedule;

use Drupal\Core\Database\Database;
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
    $query = Database::getConnection('default')->select('node_field_data', 'n');
    $query->leftJoin('node__field_room', 'r', 'r.entity_id = n.nid');
    $query->join('node__field_hour', 'h', 'h.entity_id = n.nid');
    $query->join('node__field_day', 'd', 'd.entity_id = n.nid');
    if (!empty($user)) {
      $query->join('flagging', 'f', 'f.entity_id = n.nid');
      $query->condition('f.entity_type', 'node');
      $query->condition('f.uid', $user->id());
    }
    $query->condition('n.type', ['session', 'breaks_social_activities'], 'IN');
    $query->condition('n.status', 1);
    $query->condition('d.field_day_target_id', $day['id']);
    $query->addField('n', 'nid');
    $query->addField('n', 'type');
    $query->addField('r', 'field_room_target_id');
    $query->addField('h', 'field_hour_value');
    $query->addExpression('h.field_hour_value + 0', 'order_field');
    $query->orderBy('order_field', 'ASC');
    $results = $query->execute()->fetchAll();

    $schedule = [];
    $terms = [];
    // Group by rooms only if it is the global schedule.
    if (empty($user)) {
      // Set the order for the rooms.
      $terms = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadTree('rooms');
      foreach ($terms as $term) {
        $schedule[$term->tid] = [];
      }
    }
    // Group the results by the room.
    foreach ($results as $result) {
      if ($result->type == 'breaks_social_activities' && empty($result->field_room_target_id)) {
        foreach ($terms as $term) {
          $schedule[$term->tid][] = $result->nid;
        }
      }
      else {
        // Add the room information only if it is the global schedule.
        if (!empty($user)) {
          $schedule[] = $result->nid;
        }
        else {
          $schedule[$result->field_room_target_id][] = $result->nid;
        }
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
    if ($a->field_hour_value == $b->field_hour_value) {
      return 0;
    }
    return ($a->field_hour_value < $b->field_hour_value) ? -1 : 1;
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
