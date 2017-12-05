<?php

namespace Drupal\dct_sessions\Controller;

use Drupal\contact\Entity\Message;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SprintMentorController.
 *
 * @package Drupal\dct_sessions\Controller
 */
class SprintMentorController extends ControllerBase {

  /**
   * The form_builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The entity_type.manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current_user service.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(FormBuilderInterface $formBuilder, EntityTypeManagerInterface $entityTypeManager, AccountInterface $currentUser) {
    $this->formBuilder = $formBuilder;
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
  }

  /**
   * Gets the content of the 'Be a sprint mentor' page.
   */
  public function content() {
    // To be themed at integration stage.
    $description = [
      '#markup' => $this->state()->get('dct_sprint_mentor.intro')['value'],
      '#cache' => [
        'tags' => [
          'dct_sprint_mentor'
        ],
      ],
    ];
    $form = $this->retrieveSprintMentorForm();

    return [$description, $form];
  }

  /**
   * Retrieves the rendered sprint_mentor_form contact form.
   *
   * @return array
   *   The rendered form.
   */
  public function retrieveSprintMentorForm() {
    $form_state = new FormState();

    // Retrieve the display of sponsors_contact_form form.
    $display = $this->entityTypeManager->getStorage('entity_form_display')
      ->load('contact_message.sprint_mentor_form.default');

    // Retrieve empty form object and set the display and the entity.
    $formObj = $this->entityTypeManager->getFormObject('contact_message', 'default');
    $formObj->setFormDisplay($display, $form_state);
    $formObj->setEntity(Message::create(['contact_form' => 'sprint_mentor_form']));

    return $this->formBuilder->buildForm($formObj, $form_state);

  }

}
