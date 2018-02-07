<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dct_commerce\Form\TicketGenerationForm;
use Drupal\views\ViewEntityInterface;
use Drupal\views\ViewExecutableFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TicketAdministration.
 */
class TicketAdministration extends ControllerBase {

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
   * Renders the ticket administration page.
   *
   * @return array
   *   The page render array.
   */
  public function content() {
    return [
      '#theme' => 'dct_tickets_administration',
      '#tickets_generation_form' => $this->getTicketGenerationForm(),
      '#tickets_view' => $this->getTicketsView(),
    ];
  }

  /**
   * Provides the form used to generate tickets.
   *
   * @return array
   *   The form.
   */
  protected function getTicketGenerationForm() {
    $form = $this->formBuilder()->getForm(TicketGenerationForm::class);
    return $form;
  }

  /**
   * Provides the bought tickets view.
   *
   * @return array|null
   *   The render array or null.
   */
  protected function getTicketsView() {
    $args = [$this->currentUser()];
    $view = $this->entityTypeManager()->getStorage('view')->load('bought_tickets');
    if ($view instanceof ViewEntityInterface) {
      $view = $this->viewExecutableFactory->get($view);
      if (is_object($view)) {
        $view->setArguments($args);
        $view->setDisplay('block_1');
        $view->preExecute();
        $view->execute();
        return $view->buildRenderable('block_1', $args);
      }
    }
    return NULL;
  }

}
