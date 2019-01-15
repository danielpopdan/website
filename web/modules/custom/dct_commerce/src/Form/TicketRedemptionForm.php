<?php

namespace Drupal\dct_commerce\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\dct_commerce\Controller\TicketControllerInterface;
use Drupal\dct_newsletter\Controller\MailchimpController;
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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Mailchimp service.
   *
   * @var \Drupal\dct_newsletter\Controller\MailchimpController
   */
  protected $mailchimpService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('dct_commerce.ticket_controller'),
      $container->get('entity_type.manager'),
      $container->get('dct_newsletter.mailchimp_service')
    );
  }

  /**
   * TicketRedemptionForm constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param \Drupal\dct_commerce\Controller\TicketControllerInterface $ticketController
   *   The ticket controller.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\dct_newsletter\Controller\MailchimpController $mailchimpController
   *   The Mailchimp service.
   */
  public function __construct(AccountInterface $account, TicketControllerInterface $ticketController, EntityTypeManagerInterface $entity_type_manager, MailchimpController $mailchimpController) {
    $this->currentUser = $account;
    $this->ticketController = $ticketController;
    $this->entityTypeManager = $entity_type_manager;
    $this->mailchimpService = $mailchimpController;
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
      $form['message']['#markup'] = $this->t('You have already redeemed a coupon.');
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
      $form_state->setErrorByName('code', $this->t('The coupon %code is invalid.', ['%code' => $code]));
      return;
    }
    if ($ticket->isRedeemed()) {
      $form_state->setErrorByName('code', $this->t('The coupon %code has already been redeemed.', ['%code' => $code]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $code = $form_state->getValue('code');
    $ticket = $this->ticketController->getTicketByCode($code);
    try {
      $ticket->redeem($this->currentUser);
      $ticket->save();
      // Add the attendee role to the user.
      $user = $this->entityTypeManager->getStorage('user')
        ->load($this->currentUser->id());
      $user->addRole('attendee');
        if (!empty($ticket->getOrderItem()) && $ticket->getOrderItem()->getPurchasedEntity()->get('sku')->value == 'ddd_individual_sponsor') {
            $user->addRole('individual_sponsor');
        }
      $user->save();
      $this->mailchimpService->addMailchimpUser($user->getEmail(), 'DrupalDevDays2019');
         $generation = \Drupal::service('dct_commerce.ticket_generation');
         $generation->generateTicketToUser($ticket);
      drupal_set_message($this->t('Successfully redeemed coupon %code!', ['%code' => $code]));
    }
    catch (\Exception $e) {
      drupal_set_message($this->t('The coupon %code is already redeemed!', ['%code' => $code]));
    }
  }

}
