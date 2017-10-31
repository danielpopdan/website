<?php

namespace Drupal\dct_newsletter\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\dct_newsletter\Controller\MailchimpController;
use Egulias\EmailValidator\EmailValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Manages the mailchimp service.
 */
class NewsletterSubscriptionForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The email validator service.
   *
   * @var \Egulias\EmailValidator\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * The mailchimp service.
   *
   * @var \Drupal\dct_newsletter\Controller\MailchimpController
   */
  protected $mailchimpService;

  /**
   * The email service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Constructs a NewsletterForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Egulias\EmailValidator\EmailValidatorInterface $email_validator
   *   The email validator.
   * @param \Drupal\dct_newsletter\Controller\MailchimpController $mailchimp_service
   *   The mailchimp service.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EmailValidatorInterface $email_validator, MailchimpController $mailchimp_service, MailManagerInterface $mail_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->emailValidator = $email_validator;
    $this->mailchimpService = $mailchimp_service;
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('email.validator'),
      $container->get('dct_newsletter.mailchimp_service'),
      $container->get('plugin.manager.mail')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dct_newsletter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['email'] = [
      '#type' => 'email',
      '#placeholder' => $this->t('Email'),
      '#maxlength' => 60,
      '#required' => TRUE,
    ];

    $form['error'] = [
      '#type' => 'container',
      '#prefix' => '<div class="newsletter-error-container">',
      '#suffix' => '</div>',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Subscribe'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => [$this, 'saveSubscription'],
      ],
    ];

    return $form;
  }

  /**
   * Submits the form using ajax.
   *
   * @param array $form
   *   The subscription form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Storage for the state of the form.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The result after submitting the form.
   */
  public function saveSubscription(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $error = FALSE;

    // Verifies if the field is completed.
    if (empty($form_state->getValue('email'))) {
      $html = [
        '#prefix' => '<span class="error-message">',
        '#markup' => $this->t('The email field is required.'),
        '#suffix' => '</span>',
      ];
      $error = TRUE;
    }
    else {

      // Checks if the email is in a valid format.
      if (!$this->emailValidator->isValid($form_state->getValue('email'))) {
        $html = [
          '#prefix' => '<span class="error-message">',
          '#markup' => $this->t('This email address is not valid.'),
          '#suffix' => '</span>',
        ];
        $error = TRUE;
      }

      else {
        // Checks if the email is not already used for a subscription.
        $subscribed_user = $this->entityTypeManager
          ->getStorage('user')
          ->loadByProperties(['mail' => $form_state->getValue('email')]);
        if ($subscribed_user) {
          $html = [
            '#prefix' => '<span class="error-message">',
            '#markup' => $this->t('This email address is already subscribed to the newsletter.'),
            '#suffix' => '</span>',
          ];
          $error = TRUE;
        }
      }
    }

    if (!$error) {

      // Creates an account for the email address.
      $user = [
        'name' => $form_state->getValue('email'),
        'mail' => $form_state->getValue('email'),
        'init' => $form_state->getValue('email'),
        'pass' => user_password(),
        'roles' => ['newsletter'],
        'status' => 0,
      ];

      // Creates and validates the user.
      $user = $this->entityTypeManager->getStorage('user')->create($user);
      $user_validation = $user->validate();
      $violations = $user_validation->getEntityViolations();

      // Checks if there are any violations after the validation.
      if (!empty($violations)) {
        $user->save();
        $html = [
          '#prefix' => '<span class="success-message">',
          '#markup' => $this->t('You have successfully subscribed to our newsletter!'),
          '#suffix' => '</span>',
        ];

        $html = render($html);
        $command = new ReplaceCommand('#dct-newsletter-form', $html);

        // Adds the user to the 'Target Audience' list in mailchimp.
        $this->mailchimpService->addMailchimpUser($form_state->getValue('email'), 'Target Audience');

        // Sends a confirmation email to the subscriber.
        if (!$form_state->get('anyErrors')) {
          $result = $this->mailManager->mail(
            'dct_newsletter',
            'newsletter_subscription_confirmation',
            $form_state->getValue('email'),
            $user->getPreferredLangcode(),
            [],
            NULL,
            TRUE
          );
        }

      }
      else {
        $html = [
          '#prefix' => '<span class="error-message">',
          '#markup' => $this->t('An error occured during the registration. Please try again!'),
          '#suffix' => '</span>',
        ];
        $html = render($html);
        $command = new HtmlCommand('.newsletter-error-container', $html);
      }
    }

    else {
      $html = render($html);
      $command = new HtmlCommand('.newsletter-error-container', $html);
    }

    $response->addCommand($command);

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
