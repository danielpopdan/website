<?php

namespace Drupal\dct_homepage\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Drupal\dct_map\MapProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Spread the word block.
 *
 * @Block(
 *   id = "welcome_to_city",
 *   admin_label = @Translation("Welcome to city"),
 * )
 */
class WelcomeCityBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The map provider service.
   *
   * @var \Drupal\dct_map\MapProviderInterface
   */
  protected $mapProvider;

  /**
   * WelcomeCityBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Drupal\dct_map\MapProviderInterface $map_provider
   *   The map provider service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StateInterface $state, MapProviderInterface $map_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->state = $state;
    $this->mapProvider = $map_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('state'),
      $container->get('dct_map.map_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $values = $this->state->get('dct_configuration.welcome_section');

    $link = [
      '#type' => 'link',
      '#title' => $this->t('Visit Cluj-Napoca'),
      '#url' => Url::fromRoute('<front>'),
      '#attributes' => [
        'class' => [
          'button',
        ],
        'target' => [
          '_blank',
        ],
      ],
    ];

    return [
      '#theme' => 'dct_welcome_to_city',
      '#description' => $values['description']['value'],
      '#link' => $link,
      '#youtube_url' => $values['youtube'],
      '#airport_description' => $values['airport_description']['value'],
      '#airport_map' => $this->mapProvider->getMap(),
    ];
  }

}
