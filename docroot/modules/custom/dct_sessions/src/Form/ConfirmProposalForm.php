<?php

namespace Drupal\dct_sessions\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\dct_sessions\Service\SessionProposalService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a confirmation form for the acceptance of an proposal.
 */
class ConfirmProposalForm extends ConfirmFormBase {

  /**
   * The session proposal service.
   *
   * @var \Drupal\dct_sessions\Service\SessionProposalService
   */
  protected $sessionProposalService;

  /**
   * The id of the entity to be saved as a node.
   *
   * @var int
   */
  protected $entityId;

  /**
   * {@inheritdoc}
   */
  public function __construct(SessionProposalService $session_proposal_service) {
    $this->sessionProposalService = $session_proposal_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('session_proposal')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $this->entityId = $id;

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->sessionProposalService->saveSessionProposal($this->entityId);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return "accept_proposal_confirmation_form";
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.contact_message.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Do you want to accept this proposal?');
  }

}
