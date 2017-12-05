<?php

namespace Drupal\invisible_recaptcha\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class InvisibleRecaptchaCheckForm.
 *
 * @package Drupal\invisible_recaptcha\Form
 */
class InvisibleRecaptchaCheckForm extends FormBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * InvisibleRecaptchaCheckForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Instantiates a new instance of this class.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container this instance should use.
   *
   * @return static
   *   The collection of services.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'invisible_recaptcha_checks_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Gets the values saved in config.
    $config = $this->configFactory->getEditable('invisible_recaptcha.recaptcha_points');
    $form_ids_values = $config->get('forms_ids');
    $form['form-ids'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Invisible recaptcha option'),
      '#tree' => TRUE,
      '#description' => $this->t('For removing the invisible recaptcha option, delete the form id from the textfield and click save.'),
    ];

    $form['form-ids']['fields'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'form-ids-container',
      ],
    ];

    // Gets the number of form-ids fields.
    $forms_count = $form_state->get('recaptcha_form_ids');

    $trigger = $form_state->getTriggeringElement();
    if (!$trigger || $trigger['#name'] == 'add_new_form') {
      if (empty($forms_count)) {
        if (!empty($form_ids_values)) {
          $form_state->set('recaptcha_form_ids', count($form_ids_values));
        }
        else {
          $form_state->set('recaptcha_form_ids', 1);
        }
      }
      else {
        $form_state->set('recaptcha_form_ids', $forms_count + 1);
      }
    }

    for ($i = 0; $i < $form_state->get('recaptcha_form_ids'); $i++) {
      $form['form-ids']['fields'][$i] = [
        '#type' => 'textfield',
        '#title' => $this->t('Form id'),
        '#default_value' => $form_ids_values[$i],
        '#attributes' => [
          'id' => 'form-id-' . $i,
        ],
      ];
    }

    $form['form-ids']['add-more'] = [
      '#type' => 'button',
      '#name' => 'add_new_form',
      '#value' => $this->t('Add more'),
      '#ajax' => [
        'callback' => [$this, 'addNewForm'],
        'wrapper' => 'form-ids-container',
      ],
    ];

    $form['form-ids']['submit'] = [
      '#type' => 'submit',
      '#name' => 'save_forms',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * Adds a field to the form-ids-container fieldset.
   *
   * @param array $form
   *   The structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The colection of form ids fields.
   */
  public function addNewForm(array &$form, FormStateInterface $form_state) {
    return $form['form-ids']['fields'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $forms = [];
    $values = $form_state->getValue(['form-ids', 'fields']);

    // Eliminates the empty textfields.
    foreach ($values as $form_id) {
      if (!empty($form_id)) {
        $forms[] = $form_id;
      }
    }

    $form_state->setValue(['form-ids', 'fields'], $forms);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')
      ->getEditable('invisible_recaptcha.recaptcha_points');
    $form = $form_state->getValue('form-ids');
    $config->set('forms_ids', $form['fields'])->save();
  }

}
