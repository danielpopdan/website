<?php

namespace Drupal\dct_homepage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\file\Entity\File;
use Drupal\file\Plugin\Field\FieldWidget\FileWidget;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HomepageConfigurationsForm.
 *
 * @package Drupal\dct_homepage\Form
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
  public function getFormId() {
    return 'dct_homepage_configurations';
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
      '#title' => $this->t('Newsletter description'),
      '#description' => $this->t('A short description about the event.'),
      '#default_value' => $this->state->get('dct_homepage.description'),
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => t('Image'),
      '#upload_location' => 'public://images/',
      '#default_value' => [$this->state->get('dct_homepage.share_image')],
      '#description' => t('This image is used when sharing the homepage on social networks.'),
      '#upload_validators' => [
        'file_validate_is_image' => [],
        'file_validate_extensions' => ['gif png jpg jpeg'],
        'file_validate_size' => [25600000],
      ],
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
    $fid = $form_state->getValue('image');

    if ($fid) {
      $file = File::load($fid[0]);
      $file->setPermanent();
      $file->save();
      $fid = $fid[0];
    }
    else {
      $fid = NULL;
    }

    $this->state->set('dct_homepage.share_image', $fid);
    $this->state->set('dct_homepage.date', $form_state->getValue('date'));
    $this->state->set('dct_homepage.location', $form_state->getValue('location'));
    $this->state->set('dct_homepage.description', $form_state->getValue('description'));

    drupal_set_message($this->t('The settings have been successfully saved'));
  }

}
