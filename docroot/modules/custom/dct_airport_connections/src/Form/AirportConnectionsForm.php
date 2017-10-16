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
    $titleDefault = ($this->entity->title->getValue()) ? $this->entity->title->getValue()[0] : '' ;
    $latDefault = ($this->entity->latitude->getValue()) ? $this->entity->latitude->getValue()[0] : '' ;
    $longDefault = ($this->entity->longitude->getValue()) ? $this->entity->longitude->getValue()[0] : '' ;

    $form['title'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#title' => $this->t('Title'),
      '#default_value' => $titleDefault
    ];

    $form['latitude'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#title' => $this->t('Latitude'),
      '#default_value' => $latDefault
    ];

    $form['longitude'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#title' => $this->t('Longitude'),
      '#default_value' => $longDefault
    ];

    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->set('title', $form_state->getValue('title'));
    $this->entity->set('latitude', $form_state->getValue('latitude'));
    $this->entity->set('longitude', $form_state->getValue('longitude'));

    $this->entity->save();

    $status = parent::save($form, $form_state);

    if ($status = SAVED_UPDATED) {
      drupal_set_message($this->t(
        'The airport connections %feed has been updated.',
        [
          '%feed' => $this->entity->get('title')->first()->getValue()['value']
        ]
      )
      );
    }
    else {
      drupal_set_message($this->t(
        'The airport connections %feed has been added.',
        [
          '%feed' => $this->entity->get('title')->first()->getValue()['value']
        ]
      )
      );
    }

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return $status;
  }

}
