<?php

namespace Drupal\dct_airport_connections\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AirportConnectionsForm
 *
 * @package Drupal\dct_airport_connections\Form
 */
class AirportConnectionsForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    if ($status = SAVED_UPDATED) {
      drupal_set_message($this->t('The airport connections %feed has been updated.'),
        ['%feed' => $this->entity->toLink()->toString()]);
    }
    else {
      drupal_set_message($this->t('The airport connections %feed has been added.'),
        ['%feed' => $this->entity->toLink()->toString()]);
    }

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return $status;
  }

}
