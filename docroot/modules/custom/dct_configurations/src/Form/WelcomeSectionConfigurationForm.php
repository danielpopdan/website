<?php

namespace Drupal\dct_configurations\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WelcomeSectionConfigurationForm extends FormBase{

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
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
      '#format' => isset($state_values['description']) ? $state_values['description']['format'] : ''
    ];

    $form['youtube'] = [
      '#type' => 'textfield',
      '#title' => 'Youtube Link',
      '#required' => TRUE,
      '#default_value' => isset($state_values['youtube']) ? $state_values['youtube'] : '',
    ];

    $form['airport_description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Airport Description'),
      '#required' => TRUE,
      '#default_value' => isset($state_values['airport_description']) ? $state_values['airport_description']['value'] : '',
      '#format' => isset($state_values['airport_description']) ? $state_values['airport_description']['format'] : ''
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

    drupal_set_message($this->t('The settings have been successfully saved'));
  }
}
