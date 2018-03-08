<?php

namespace Drupal\dct_schedule;

use Drupal\user\UserInterface;
use RedUNIT\Base\Xnull;

/**
 * Interface ScheduleProviderInterface.
 *
 * @package Drupal\dct_schedule
 */
interface ScheduleProviderInterface {

  /**
   * Returns the schedule based on the day and user.
   *
   * @param string $day
   *   The day to get the schedule for.
   * @param \Drupal\user\UserInterface|null $user
   *   The user for which we should retrieve the schedule.
   *
   * @return array
   *   The schedule.
   */
  public function getSchedule($day, UserInterface $user = NULL);

  /**
   * Returns the conference days.
   *
   * @return array
   *   The conference days.
   */
  public function getConferenceDays();

  /**
   * Returns the conference rooms.
   *
   * @return array
   *   The conference rooms.
   */
  public function getConferenceRooms();

}
