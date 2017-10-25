<?php

namespace Drupal\dct_homepage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SpreadTheWordConfigurationForm.
 *
 * @package Drupal\dct_homepage\Form
 */
class SpreadTheWordConfigurationForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a SpreadTheWordConfigurationForm object.
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
  public function getFormId() {
    return 'dct_spread_the_word_configurations';
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type' => 'text_format',
      '#format' => !empty($this->state->get('dct_spread_the_word.description')['format']) ? $this->state->get('dct_spread_the_word.description')['format'] : 'full_html',
      '#title' => $this->t('Spread the word description'),
      '#description' => $this->t('A short description to spread the word.'),
      '#default_value' => !empty($this->state->get('dct_spread_the_word.description')['value']) ? $this->state->get('dct_spread_the_word.description')['value'] : '',
    ];

    $form['link'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Link'),
      '#description' => $this->t('Set a link to spread the word'),
    ];

    $form['link']['link_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->state->get('dct_spread_the_word.link_title'),
      '#maxlength' => 1024,
      '#required' => TRUE,
    ];

    $form['link']['link_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Url'),
      '#default_value' => $this->state->get('dct_spread_the_word.link_url'),
      '#maxlength' => 1024,
      '#required' => TRUE,
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
    $this->state->set('dct_spread_the_word.description', $form_state->getValue('description'));
    $this->state->set('dct_spread_the_word.link_title', $form_state->getValue('link_title'));
    $this->state->set('dct_spread_the_word.link_url', $form_state->getValue('link_url'));

    drupal_set_message($this->t('The settings have been successfully saved'));
  }

}
