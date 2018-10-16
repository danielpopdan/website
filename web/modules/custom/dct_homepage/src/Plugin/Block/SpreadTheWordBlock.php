<?php

namespace Drupal\dct_homepage\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Spread the word block.
 *
 * @Block(
 *   id = "spread_the_word_block",
 *   admin_label = @Translation("Spread the word"),
 * )
 */
class SpreadTheWordBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * SpreadTheWordBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $description = !empty($this->state->get('dct_spread_the_word.description')['value']) ? $this->state->get('dct_spread_the_word.description')['value'] : '';
    $uri = $this->state->get('dct_spread_the_word.link_url');

    $link = [
      '#type' => 'link',
      '#title' => $this->state->get('dct_spread_the_word.link_title'),
      '#url' => ($uri) ? Url::fromUri($uri) : Url::fromRoute('<front>'),
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
      '#theme' => 'dct_spread_the_word_block',
      '#description' => $description,
      '#link' => $link,
      '#share' => [
        '#addtoany_html' => addtoany_create_data(),
        '#theme' => 'addtoany_standard',
        '#cache' => [
          'tags' => ['dct_homepage.spread_word'],
          'contexts' => ['url'],
        ],
      ],
    ];
  }

}
