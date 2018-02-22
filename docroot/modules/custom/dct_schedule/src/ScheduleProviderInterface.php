<?php

namespace Drupal\dct_schedule;

use Drupal\user\UserInterface;
use RedUNIT\Base\Xnull;

interface ScheduleProviderInterface {

  /**
   * Returns the schedule based on the day and user.
   *
   * @param string $day
   *   The day to get the schedule for.
   * @param \Drupal\user\UserInterface|NULL $user
   *   The user for which we should retrieve the schedule.
   *
   * @return array
   *   The schedule.
   */
  public function getSchedule($day, UserInterface $user = NULL);

}
