<?php

namespace Drupal\dct_schedule\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\dct_schedule\ScheduleProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ScheduleController.
 *
 * @package Drupal\dct_schedule\Controller
 */
class ScheduleController extends ControllerBase {

  /**
   * The schedule provider service.
   *
   * @var \Drupal\dct_schedule\ScheduleProviderInterface
   */
  protected $scheduleProvider;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * ScheduleController constructor.
   *
   * @param \Drupal\dct_schedule\ScheduleProviderInterface $schedule_provider
   *   The schedule provider service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ScheduleProviderInterface $schedule_provider, EntityTypeManagerInterface $entity_type_manager) {
    $this->scheduleProvider = $schedule_provider;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dct_schedule.schedule_provider'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns the schedule page.
   */
  public function content() {
    // Get the conference days.
    $days = $this->scheduleProvider->getConferenceDays();
    // Get the schedule for the first day.
    $first_day_schedule = $this->scheduleProvider->getSchedule($days[0]);
    $sessions = [];
    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $node_storage = $this->entityTypeManager->getStorage('node');
    // Create the session list for the first day.
    foreach ($first_day_schedule as $room => $nids) {
      foreach ($nids as $nid) {
        $node = $node_storage->load($nid);
        $sessions[$room][] = $view_builder->view($node, 'teaser');
      }
    }

    return [
      '#theme' => 'dct_schedule',
      '#sessions' => $sessions,
    ];
  }

}
