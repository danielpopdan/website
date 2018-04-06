<?php

namespace Drupal\dct_schedule\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\dct_schedule\ScheduleProviderInterface;
use Drupal\user\UserInterface;
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
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * ScheduleController constructor.
   *
   * @param \Drupal\dct_schedule\ScheduleProviderInterface $schedule_provider
   *   The schedule provider service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(ScheduleProviderInterface $schedule_provider, EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user) {
    $this->scheduleProvider = $schedule_provider;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dct_schedule.schedule_provider'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * Returns the schedule page.
   */
  public function content() {
    // Get the conference days.
    $days = $this->scheduleProvider->getConferenceDays();
    $sessions = [];
    foreach ($days as $day) {
      $sessions[strtolower($day['name'])] = $this->getDaySchedule($day);
    }
    $render_days = [];
    foreach ($days as $id => $day) {
      $render_days[strtolower($day['name'])] = $day['name'];
    }

    return [
      '#theme' => 'dct_schedule',
      '#sessions' => $sessions,
      '#days' => $render_days,
      '#rooms' => $this->scheduleProvider->getConferenceRooms(),
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Returns the schedule for the current user.
   *
   * @return array
   *   The schedule.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function mySchedule() {
    $user = $this->entityTypeManager->getStorage('user')
      ->load($this->currentUser->id());
    // Get the conference days.
    $days = $this->scheduleProvider->getConferenceDays();
    $sessions = [];
    foreach ($days as $day) {
      $sessions[strtolower($day['name'])] = $this->getDaySchedule($day, $user);
    }

    $render_days = [];
    foreach ($days as $id => $day) {
      $render_days[strtolower($day['name'])] = $day['name'];
    }

    return [
      '#theme' => 'dct_my_schedule',
      '#sessions' => $sessions,
      '#days' => $render_days,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Returns the day schedule output.
   *
   * @param array $day
   *   The day to return for.
   * @param \Drupal\user\UserInterface $user
   *   The user to to get the schedule for.
   *
   * @return array
   *   The resulted content.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  private function getDaySchedule(array $day, UserInterface $user = NULL) {
    // Get the schedule for the first day.
    $first_day_schedule = $this->scheduleProvider->getSchedule($day, $user);
    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $node_storage = $this->entityTypeManager->getStorage('node');
    $sessions = [];
    // Create the session list for the first day.
    foreach ($first_day_schedule as $room => $nids) {
      if (empty($user)) {
        foreach ($nids as $nid) {
          $node = $node_storage->load($nid);
          $sessions[$room][] = $view_builder->view($node, 'teaser');
        }
      }
      else {
        $node = $node_storage->load($nids);
        if (empty($sessions[$nids])) {
          $sessions[$nids] = $view_builder->view($node, 'teaser');
        }
      }
    }

    return $sessions;
  }

}
