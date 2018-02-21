<?php

namespace Drupal\dct_bills\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class InvoiceNumberConfigurationForm.
 *
 * @package Drupal\dct_homepage\Form
 */
class InvoiceNumberConfigurationForm extends FormBase {

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
    return 'dct_bills_number_configurations';
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
    $form['invoice_number'] = [
      '#type' => 'textfield',
      '#attributes' => [
        'type' => 'number',
      ],
      '#title' => $this->t('Invoice number'),
      '#description' => $this->t('Modify this value carefully. There shouldn\'t be 2 invoices with the same number.'),
      '#default_value' => !empty($this->state->get('dct_bills.bill_no')) ? $this->state->get('dct_bills.bill_no') : '0',
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
    $this->state->set('dct_bills.bill_no', $form_state->getValue('invoice_number'));

    drupal_set_message($this->t('The settings have been successfully saved'));
  }

}
