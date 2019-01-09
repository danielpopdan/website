<?php

namespace Drupal\dct_commerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ValidateTicketForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'dct_commerce_validate_ticket';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('VALIDATE'),
            '#button_type' => 'primary',
            '#attributes' => [
                'class' => [
                    'button',
                    'button-primary',
                ],
            ],
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $user = \Drupal::routeMatch()->getParameter('user');
        $user = \Drupal::entityTypeManager()->getStorage('user')->load($user->id());
        $user->set('field_attended', true);
        $user->save();

        $form_state->setRedirect('dct_commerce.scan_ticket', ['user' => $user->id(), 'status' => 'success']);
    }
}
