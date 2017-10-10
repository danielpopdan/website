<?php

namespace Drupal\dct_configurations\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HomepageConfigurationsForm
 *
 * @package Drupal\dct_configurations\Form
 */
class HomepageConfigurationsForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a HomepageConfigurationsForm object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

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
  public function getFormId() {
    return 'dct_homepage_configurations';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['date'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Date'),
      '#description' => $this->t('The date when DrupalCamp Transylvania will take place.'),
      '#required' => TRUE,
      '#default_value' => $this->state->get('dct_homepage.date'),
    ];

    $form['location'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location'),
      '#description' => $this->t('The location where DrupalCamp Transylvania will take place.'),
      '#required' => TRUE,
      '#default_value' => $this->state->get('dct_homepage.location'),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Short description'),
      '#description' => $this->t('A short description about the event.'),
      '#default_value' => $this->state->get('dct_homepage.description'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configurations'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->state->set('dct_homepage.date', $form_state->getValue('date'));
    $this->state->set('dct_homepage.location', $form_state->getValue('location'));
    $this->state->set('dct_homepage.description', $form_state->getValue('description'));
  }

}
