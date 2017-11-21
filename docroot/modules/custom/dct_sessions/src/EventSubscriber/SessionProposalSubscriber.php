<?php

namespace Drupal\dct_sessions\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class SessionProposalSubscriber.
 *
 * @package Drupal\dct_sessions\EventSubscriber
 */
class SessionProposalSubscriber implements EventSubscriberInterface {

  /**
   * The service for managing the current account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * The service for managing the result of routing.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * SessionProposalSubscriber constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The service for managing the current account.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The service for managing the result of routing.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type service.
   */
  public function __construct(AccountProxyInterface $current_user, RouteMatchInterface $route_match, EntityTypeManagerInterface $entity_type_manager) {
    $this->account = $current_user;
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['userRedirect'];
    return $events;
  }

  /**
   * Redirects the anonymous user on the login page.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The response event.
   */
  public function userRedirect(GetResponseEvent $event) {

    // Gets the session proposal form id.
    $entity = $this->getContactFormEntity('/contact/session_proposal_form');
    $form_id = $entity->get('id');

    // Gets the current route name.
    $route_name = $this->routeMatch->getRouteName();

    // Checks if the current user is annonymous and if the current form is
    // the session proposal form.
    if ($this->account->isAnonymous() && $route_name == 'entity.contact_form.canonical' && $form_id == 'session_proposal_form') {

      // Sets destination query parameter the current page.
      $query = [];
      $query['destination'] = Url::fromRoute('<current>')->toString();
      $login_uri = Url::fromRoute('user.login', [], ['query' => $query])
        ->toString();

      // Sets the redirect option to the login page.
      $returnResponse = new RedirectResponse($login_uri, Response::HTTP_FOUND);
      $event->setResponse($returnResponse);
    }
  }

  /**
   * Gets the contact form entity.
   *
   * @param string $internal_uri
   *   The internal path for the contact form.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The contatct form entity.
   */
  public function getContactFormEntity($internal_uri) {
    $params = Url::fromUri("internal:" . $internal_uri)
      ->getRouteParameters();
    $entity_type = key($params);
    // Gets the entity based on the internal uri.
    $entity = $this->entityTypeManager->getStorage($entity_type)
      ->load($params[$entity_type]);

    return $entity;
  }

}
