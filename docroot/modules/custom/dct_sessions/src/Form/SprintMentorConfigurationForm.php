<?php

namespace Drupal\dct_sessions\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SprintMentorConfigurationForm.
 *
 * @package Drupal\dct_sessions\Form
 */
class SprintMentorConfigurationForm extends FormBase {

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
  protected $cacheTagsInvalidator;

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
  public function __construct(StateInterface $state, CacheTagsInvalidatorInterface $cacheTagsInvalidator) {
    $this->state = $state;
    $this->cacheTagsInvalidator = $cacheTagsInvalidator;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dct_sprint_mentor_configurations';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['intro'] = [
      '#type' => 'text_format',
      '#format' => !empty($this->state->get('dct_sprint_mentor.intro')['format']) ? $this->state->get('dct_sprint_mentor.intro')['format'] : 'full_html',
      '#title' => $this->t('Spread the word description'),
      '#description' => $this->t('A short description to spread the word.'),
      '#default_value' => !empty($this->state->get('dct_sprint_mentor.intro')['value']) ? $this->state->get('dct_sprint_mentor.intro')['value'] : '',
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
    if ($form_state->getValues()) {
      $this->state->set('dct_sprint_mentor.intro', $form_state->getValues()['intro']);
      $this->cacheTagsInvalidator->invalidateTags(['dct_sprint_mentor']);

      drupal_set_message($this->t('The settings have been successfully saved'));
    }
  }

}
