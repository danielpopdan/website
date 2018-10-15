<?php

namespace Drupal\dct_paragraphs\Plugin\ViewBuilder;

use Drupal\contact\Entity\Message;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\entity_view_builder\EntityViewBuilderBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Definition for sponsor_contact_form view builder alter.
 *
 * @EntityViewBuilder(
 *   id = "sponsor_contact_form_view_builder",
 *   bundle = "sponsor_contact_form"
 * )
 */
class SponsorsContactViewBuilder extends EntityViewBuilderBase implements ContainerFactoryPluginInterface {

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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $formBuilder, EntityTypeManagerInterface $entityTypeManager) {
    $this->formBuilder = $formBuilder;
    $this->entityTypeManager = $entityTypeManager;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function build(array &$build) {

    // Retrieve the display of sponsors_contact_form form.
    $display = $this->entityTypeManager->getStorage('entity_form_display')
      ->load('contact_message.sponsors_contact_form.default');

    $form_state = new FormState();

    // Retrieve empty form object and set the display and the entity.
    $formObj = $this->entityTypeManager->getFormObject('contact_message', 'default');
    $formObj->setFormDisplay($display, $form_state);
    $formObj->setEntity(Message::create(['contact_form' => 'sponsors_contact_form']));

    $build[] = $this->formBuilder->buildForm($formObj, $form_state);
  }

}
