<?php

namespace Drupal\dct_sessions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\dct_sessions\Service\SessionProposalService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SessionProposalController.
 *
 * @package Drupal\dct_sessions\Controller
 */
class SessionProposalController extends ControllerBase {

  /**
   * The form_builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public function __construct(FormBuilderInterface $formBuilder, SessionProposalService $session_proposal_service) {
    $this->formBuilder = $formBuilder;
    $this->sessionProposalService = $session_proposal_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('session_proposal')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getAcceptContent($contact_message_id) {
    // Gets the confirmation form.
    $form = $this->formBuilder->getForm('\Drupal\dct_sessions\Form\AcceptSessionProposalForm', $contact_message_id);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getRejectContent($contact_message_id) {
    // Gets the confirmation form.
    $form = $this->formBuilder->getForm('\Drupal\dct_sessions\Form\RejectSessionProposalForm', $contact_message_id);

    return $form;
  }

}
