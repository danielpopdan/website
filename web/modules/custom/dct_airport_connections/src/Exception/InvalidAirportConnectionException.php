<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class InvalidAirportConnectionException.
 *
 * @package Drupal\dct_airport_connections\Exception
 */
class InvalidAirportConnectionException extends HttpException {

  public const MESSAGE = 'Invalid airport connection value.';

  /**
   * InvalidAirportConnectionException constructor.
   */
  public function __construct() {
    parent::__construct(Response::HTTP_BAD_REQUEST, self::MESSAGE);
  }

}
