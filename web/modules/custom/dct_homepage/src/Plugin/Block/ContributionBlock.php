<?php

namespace Drupal\dct_homepage\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Contribution block.
 *
 * @Block(
 *   id = "contribution_block",
 *   admin_label = @Translation("Contribution block"),
 * )
 */
class ContributionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * SpreadTheWordBlock constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $link = [
      '#type' => 'link',
      '#title' => $this->t('Propose a workshop'),
      '#url' => Url::fromUri('internal:/call-for-papers'),
      '#attributes' => [
        'class' => [
          'button',
        ],
        'target' => [
          '_blank',
        ],
      ],
    ];

      $contribution = [
          '#type' => 'link',
          '#title' => $this->t('Sign up to contribute'),
          '#url' => Url::fromUri('internal:/become-a-mentor'),
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
      '#theme' => 'dct_contribution_block',
      '#cfp' => $link,
      '#contribution' => $contribution,
    ];
  }

}
