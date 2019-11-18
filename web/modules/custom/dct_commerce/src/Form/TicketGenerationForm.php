<?php

namespace Drupal\dct_commerce\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\dct_commerce\Controller\TicketControllerInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TicketGenerationForm.
 */
class TicketGenerationForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface|null
   */
  protected $currentUser;

  /**
   * The ticket controller.
   *
   * @var \Drupal\dct_commerce\Controller\TicketControllerInterface
   */
  protected $ticketController;

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManager
   */
  protected $mailManager;

  /**
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('dct_commerce.ticket_controller'),
      $container->get('plugin.manager.mail'),
      $container->get('messenger')
    );
  }

  /**
   * TicketRedemptionForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\dct_commerce\Controller\TicketControllerInterface $ticketController
   *   The ticket controller.
   * @param \Drupal\Core\Mail\MailManager $mailManager
   *   The mail manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, TicketControllerInterface $ticketController, MailManager $mailManager, MessengerInterface $messenger) {
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $this->entityTypeManager->getStorage('user')->load($this->currentUser()->id());
    $this->ticketController = $ticketController;
    $this->mailManager = $mailManager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dct_commerce_ticket_generation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['ticket_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of coupons'),
      '#desctiption' => $this->t('The number of coupons you want to generate'),
      '#required' => TRUE,
    ];
    $form['user'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'user',
      '#title' => $this->t('The coupon creator'),
      '#default_value' => $this->currentUser,
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $count = $form_state->getValue('ticket_count');
    if (is_numeric($count) && intval($count) < 1) {
      $form_state->setErrorByName('ticket_count', $this->t('You cannot generate @count tickets', ['@count' => $count]));
    }
    if (!($this->entityTypeManager->getStorage('user')->load($form_state->getValue('user')) instanceof User)) {
      $form_state->setErrorByName('user', $this->t('Invalid user'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $count = intval($form_state->getValue('ticket_count'));
    $author = $this->entityTypeManager->getStorage('user')->load($form_state->getValue('user'));
    $coupons = $this->generateCoupons($count, $author);
    if (!empty($coupons)) {
      if ($this->sendCoupons($coupons, $author)) {
        $this->messenger->addMessage($this->t('Successfully generated coupons.'));
      }
      else {
        $this->messenger->addMessage($this->t('The coupons were generated, but the email failed to send.'), 'warning');
      }
    }
    else {
      $this->messenger->addMessage($this->t('Failed to generate the coupons.'));
    }
  }

  /**
   * Generates tickets based on the form data.
   *
   * @param int $count
   *   The ticket count.
   * @param \Drupal\Core\Session\AccountInterface $author
   *   The ticket owner.
   *
   * @return array
   *   The generated coupons.
   */
  private function generateCoupons($count, AccountInterface $author) {
    $tickets = [];
    for ($i = 0; $i < $count; $i++) {
      $ticket = $this->ticketController->createTicket($author, NULL);
      $ticket->save();
      $tickets[] = $ticket;
    }
    return $tickets;
  }

  /**
   * Sends a mail containing the ticket codes.
   *
   * @param array $tickets
   *   The generated tickets.
   * @param \Drupal\Core\Session\AccountInterface $author
   *   The ticket owner.
   *
   * @return bool
   *   The mail delivery result.
   */
  private function sendCoupons(array $tickets, AccountInterface $author) {
    $params = [
      'code' => NULL,
      'redeem_link' => Url::fromRoute('dct_commerce.ticket_redemption_code')->setAbsolute(TRUE)->toString(TRUE)->getGeneratedUrl(),
    ];
    // Send mail containing all of the codes
    // to the user who purchased the tickets.
    $params['tickets'] = $tickets;
    $params['account'] = $author;
    $mailResult = $this->mailManager->doMail(
      'dct_commerce',
      'ticket_buyer',
      $author->getEmail(),
      $author->getPreferredLangcode(),
      $params,
      NULL,
      TRUE
    );
    return $mailResult['result'] === 1;
  }

}
