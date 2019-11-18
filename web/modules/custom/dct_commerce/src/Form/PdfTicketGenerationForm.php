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
use Drupal\dct_commerce\TicketGenerationService;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TicketGenerationForm.
 */
class PdfTicketGenerationForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

    /**
     * The entity type manager service.
     *
     * @var \Drupal\dct_commerce\TicketGenerationServiceInterface
     */
  protected $ticketGenerator;

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
      $container->get('dct_commerce.ticket_generation'),
      $container->get('messenger')
    );
  }

  /**
   * TicketRedemptionForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param TicketGenerationService $ticketGeneration
   *   The ticket generation service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, TicketGenerationService $ticketGeneration, MessengerInterface $messenger) {
    $this->entityTypeManager = $entityTypeManager;
    $this->ticketGenerator = $ticketGeneration;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dct_commerce_pdf_ticket_generation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['ticket_id'] = [
      '#type' => 'number',
      '#title' => $this->t('Ticket ID'),
      '#desctiption' => $this->t('The ticket ID to generate the pdf.'),
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
    $ticket = $this->entityTypeManager->getStorage('dct_commerce_ticket')->load($form_state->getValue('ticket_id'));
    if(empty($ticket)) {
        $form_state->setErrorByName('ticket_id', t('The ticket id is not valid'));
    }
    else {
        $form_state->set('ticket', $ticket);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      $this->ticketGenerator->generateTicketToUser($form_state->get('ticket'));

      $this->messenger->addMessage('The ticket was generated successfully!');
  }
}
