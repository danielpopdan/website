<?php

declare(strict_types = 1);

namespace Drupal\dct_airport_connections\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\dct_airport_connections\Exception\InvalidAirportConnectionException;
use Drupal\dct_airport_connections\Repository\AirportConnectionRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AirportConnectionForm.
 *
 * @package Drupal\dct_airport_connections\Form
 */
class AirportConnectionForm extends ContentEntityForm {

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The airport connection repository.
   *
   * @var \Drupal\dct_airport_connections\Repository\AirportConnectionRepositoryInterface
   */
  protected $airportConnectionRepository;

  /**
   * AirportConnectionsForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entityRepository
   *   The entity repository.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\dct_airport_connections\Repository\AirportConnectionRepositoryInterface $airportConnectionRepository
   *   The airport connection repository.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface|null $entityTypeBundleInfo
   *   The entity bundle info.
   * @param \Drupal\Component\Datetime\TimeInterface|null $time
   *   The datetime service.
   */
  public function __construct(
    EntityRepositoryInterface $entityRepository,
    MessengerInterface $messenger,
    AirportConnectionRepositoryInterface $airportConnectionRepository,
    ?EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    ?TimeInterface $time
  ) {
    parent::__construct($entityRepository, $entityTypeBundleInfo, $time);
    $this->messenger = $messenger;
    $this->airportConnectionRepository = $airportConnectionRepository;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('messenger'),
      $container->get('dct_airport_connections.airport_connection_repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['title'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Title'),
      '#default_value' => $this->entity->label() ?? '',
    ];
    $form['latitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Latitude'),
      '#default_value' => $this->entity->getLatitude() ?? '',
    ];
    $form['longitude'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Longitude'),
      '#default_value' => $this->entity->getLongitude() ?? '',
    ];
    $form['isOrigin'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Is origin'),
      '#description' => $this->t(
        'Mark this connection point as an origin. It will be available as an option for the origin select input for other airport connection entities.'
      ),
      '#default_value' => $this->entity->isOrigin() ?? '',
    ];
    $form['origin'] = [
      '#type' => 'select',
      '#title' => $this->t('Origin'),
      '#options' => [NULL => 'None'] + $this->airportConnectionRepository->getOrigins(),
      '#required' => FALSE,
      '#default_value' => $this->entity->getOrigin() ?? '',
    ];
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    // Save the entity.
    $this->entity->set('title', $form_state->getValue('title'));
    $this->entity->set('latitude', $form_state->getValue('latitude'));
    $this->entity->set('longitude', $form_state->getValue('longitude'));
    $this->entity->set('isOrigin', $form_state->getValue('isOrigin'));
    $this->entity->set('origin', $form_state->getValue('origin'));

    $status = parent::save($form, $form_state);

    // Validate status.
    $validStatuses = [SAVED_NEW, SAVED_UPDATED];
    if (!in_array($status, $validStatuses)) {
      throw new InvalidAirportConnectionException();
    }

    // Set the confirmation message.
    $messages = [
      SAVED_NEW => 'The airport connection <em>@connection</em> has been created.',
      SAVED_UPDATED => 'The airport connection <em>@connection</em> has been updated.',
    ];
    $messageReplacements = [
      '@connection' => $this->entity->label(),
    ];
    $this->messenger->addMessage($this->t($messages[$status], $messageReplacements));

    // Set the redirect.
    $form_state->setRedirect('view.airport_connection.list');

    return $status;
  }

}
