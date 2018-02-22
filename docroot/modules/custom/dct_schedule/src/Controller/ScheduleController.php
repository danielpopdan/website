<?php

namespace Drupal\dct_schedule\Controller;

use Drupal\Core\Controller\ControllerBase;
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
   * ScheduleController constructor.
   *
   * @param \Drupal\dct_schedule\ScheduleProviderInterface $schedule_provider
   *   The schedule provider service.
   */
  public function __construct(ScheduleProviderInterface $schedule_provider) {
    $this->scheduleProvider = $schedule_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('dct_schedule.schedule_provider')
    );
  }

  /**
   * Returns the schedule page.
   */
  public function content() {
    return [
      '#theme' => 'dct_schedule',
      '#sessions' => NULL,
    ];
  }

}
