<?php

namespace Drupal\invisible_recaptcha\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Manages the configurations for invisible recaptcha.
 */
class InvisibleRecaptchaConfigurationsForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritdoc}
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
    return 'invisible_recaptcha_configurations';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['site-key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site key'),
      '#description' => $this->t('The key for client side integration.'),
      '#required' => TRUE,
      '#default_value' => $this->state->get('invisible_recaptcha.site_key'),
    ];

    $form['secret-key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret key'),
      '#description' => $this->t('The secret key for server side integration.'),
      '#required' => TRUE,
      '#default_value' => $this->state->get('invisible_recaptcha.secret_key'),
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
    // Saves the site and the secret keys in state.
    $this->state->set('invisible_recaptcha.site_key', $form_state->getValue('site-key'));
    $this->state->set('invisible_recaptcha.secret_key', $form_state->getValue('secret-key'));

    drupal_set_message($this->t('The settings have been successfully saved!'));
  }

}
