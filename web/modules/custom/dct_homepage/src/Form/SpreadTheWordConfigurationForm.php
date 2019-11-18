<?php

namespace Drupal\dct_homepage\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
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
   * The cache_tags.invalidator service.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheInvalidator;

  /**
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a SpreadTheWordConfigurationForm object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cacheInvalidator
   *   The cache_tags.invalidator service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   */
  public function __construct(StateInterface $state, CacheTagsInvalidatorInterface $cacheInvalidator, MessengerInterface $messenger) {
    $this->state = $state;
    $this->cacheInvalidator = $cacheInvalidator;
    $this->messenger = $messenger;
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
      $container->get('state'),
      $container->get('cache_tags.invalidator'),
      $container->get('messenger')
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

    $this->cacheInvalidator->invalidateTags(['dct_homepage.spread_word']);

    $this->messenger->addMessage($this->t('The settings have been successfully saved'));
  }

}
