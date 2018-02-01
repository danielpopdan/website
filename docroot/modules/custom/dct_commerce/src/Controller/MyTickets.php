<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dct_commerce\Entity\Ticket;
use Drupal\views\ViewEntityInterface;
use Drupal\views\ViewExecutableFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MyTickets.
 */
class MyTickets extends ControllerBase {

  /**
   * The ticket controller.
   *
   * @var \Drupal\dct_commerce\Controller\TicketController
   */
  protected $ticketController;

  /**
   * The view executable service.
   *
   * @var \Drupal\views\ViewExecutableFactory
   */
  protected $viewExecutableFactory;

  /**
   * MyTickets constructor.
   *
   * @param \Drupal\dct_commerce\Controller\TicketController $ticketController
   *   The tickets controller.
   * @param \Drupal\views\ViewExecutableFactory $viewExecutableFactory
   *   The view executable service.
   */
  public function __construct(TicketController $ticketController, ViewExecutableFactory $viewExecutableFactory) {
    $this->ticketController = $ticketController;
    $this->viewExecutableFactory = $viewExecutableFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('dct_commerce.ticket_controller'),
      $container->get('views.executable')
    );
  }

  /**
   * Renders the tickets page.
   *
   * @return array
   *   The render array.
   */
  public function renderMyTicketsPage() {
    return [
      '#theme' => 'dct_my_tickets',
      '#bought_tickets_view' => $this->getBoughtTicketsView(),
      '#redeemed_ticket' => $this->getRedeemedTicketCode(),
    ];
  }

  /**
   * Provides the code redeemed by the current user.
   *
   * @return string|null
   *   The code string, or null if nothing was redeemed.
   */
  protected function getRedeemedTicketCode() {
    if ($this->currentUser()->isAuthenticated()) {
      $ticket = $this->ticketController->getTicketByRedeemer($this->currentUser);
      if ($ticket instanceof Ticket) {
        return $ticket->getCode();
      }
    }
    return NULL;
  }

  /**
   * Provides the bought tickets view.
   *
   * @return array|null
   *   The render array or null.
   */
  protected function getBoughtTicketsView() {
    $args = [$this->currentUser()];
    $view = $this->entityTypeManager()->getStorage('view')->load('bought_tickets');
    if ($view instanceof ViewEntityInterface) {
      $view = $this->viewExecutableFactory->get($view);
      if (is_object($view)) {
        $view->setArguments($args);
        $view->setDisplay('page');
        $view->preExecute();
        $view->execute();
        return $view->buildRenderable('page', $args);
      }
    }
    return NULL;
  }

}
