<?php

namespace Drupal\dct_homepage\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WelcomeSectionConfigurationForm.
 *
 * @package Drupal\dct_homepage\Form
 */
class WelcomeSectionConfigurationForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The cache_tags.invalidator service.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheInvalidator;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(StateInterface $state, CacheTagsInvalidatorInterface $cacheInvalidator) {
    $this->state = $state;
    $this->cacheInvalidator = $cacheInvalidator;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dct_configuration.welcome_section_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $state_values = $this->state->get('dct_configuration.welcome_section');

    $form['description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#required' => TRUE,
      '#default_value' => isset($state_values['description']) ? $state_values['description']['value'] : '',
      '#format' => isset($state_values['description']) ? $state_values['description']['format'] : 'full_html',
    ];

    $form['youtube'] = [
      '#type' => 'textfield',
      '#title' => 'Youtube Link',
      '#required' => TRUE,
      '#default_value' => isset($state_values['youtube']) ? $state_values['youtube'] : '',
    ];

    $form['visit'] = [
      '#type' => 'textfield',
      '#title' => 'Visit Cluj-Napoca Node ID',
      '#required' => TRUE,
      '#default_value' => isset($state_values['visit']) ? $state_values['visit'] : '',
    ];


    $form['airport_description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Airport Description'),
      '#required' => TRUE,
      '#default_value' => isset($state_values['airport_description']) ? $state_values['airport_description']['value'] : '',
      '#format' => isset($state_values['airport_description']) ? $state_values['airport_description']['format'] : 'full_html'
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration')
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->state->set('dct_configuration.welcome_section', $values);

    // Add this specific cache tag for welcome section block.
    $this->cacheInvalidator->invalidateTags(['dct_homepage.welcome_section']);

    drupal_set_message($this->t('The settings have been successfully saved'));
  }

}
