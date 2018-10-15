<?php

namespace Drupal\dct_user\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class NotAuthenticatedEventSubscriber.
 *
 * @package Drupal\dct_user\EventSubscriber
 */
class NotAuthenticatedEventSubscriber implements EventSubscriberInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The routes this subscriber should act on.
   */
  const ROUTES = [
    'dct_commerce.ticket_redemption_code',
    'entity.commerce_product.canonical',
  ];

  /**
   * Constructs a new event subscriber.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The current request stack.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current routeMatch.
   */
  public function __construct(AccountInterface $current_user, RequestStack $requestStack, RouteMatchInterface $routeMatch) {
    $this->request = $requestStack->getCurrentRequest();
    $this->currentUser = $current_user;
    $this->routeMatch = $routeMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::EXCEPTION][] = ['onKernelException'];
    return $events;
  }

  /**
   * Redirects on 403 Access Denied kernel exceptions.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The Event to process.
   */
  public function onKernelException(GetResponseEvent $event) {
    $exception = $event->getException();
    if ($exception instanceof AccessDeniedHttpException && $this->currentUser->isAnonymous() && in_array($this->routeMatch->getRouteName(), static::ROUTES)) {
      $destination = [];
      if (!empty($this->request->getRequestUri())) {
        $destination = ['destination' => $this->request->getRequestUri()];
      }
      $url = Url::fromRoute('dct_user.error_page', $destination);
      $response = new RedirectResponse($url->toString(TRUE)->getGeneratedUrl());
      $event->setResponse($response);
    }
  }

}
