<?php

namespace Drupal\dct_commerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\dct_commerce\Controller\TicketControllerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TicketRedemptionForm.
 */
class TicketRedemptionForm extends FormBase {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The ticket controller.
   *
   * @var \Drupal\dct_commerce\Controller\TicketControllerInterface
   */
  protected $ticketController;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('dct_commerce.ticket_controller')
    );
  }

  /**
   * TicketRedemptionForm constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param \Drupal\dct_commerce\Controller\TicketControllerInterface $ticketController
   *   The ticket controller.
   */
  public function __construct(AccountInterface $account, TicketControllerInterface $ticketController) {
    $this->currentUser = $account;
    $this->ticketController = $ticketController;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dct_commerce_ticket_redemption_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $ticket = $this->ticketController->getTicketByRedeemer($this->currentUser);
    if (!empty($ticket)) {
      $form['message']['#markup'] = $this->t('You have redeemed your code.');
      return $form;
    }

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Code'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Redeem Code'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $code = $form_state->getValue('code');
    $ticket = $this->ticketController->getTicketByCode($code);
    if (empty($ticket)) {
      $form_state->setErrorByName('code', $this->t('The code %code is invalid.', ['%code' => $code]));
      return;
    }
    if ($ticket->isRedeemed()) {
      $form_state->setErrorByName('code', $this->t('The code %code has already been redeemed.', ['%code' => $code]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $code = $form_state->getValue('code');
    $ticket = $this->ticketController->getTicketByCode($code);
    $ticket->redeem($this->currentUser);
    $ticket->save();

    drupal_set_message($this->t('Successfully redeemed coupon %code!', ['%code' => $code]));
  }

}
