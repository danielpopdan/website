<?php

namespace Drupal\dct_homepage\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dct_homepage\Form\DiamondSponsorsConfigurationsForm;
use Drupal\file\Entity\File;

/**
 * Provides Diamond Sponsors Footer Block.
 *
 * @Block(
 *   id = "diamond_sponsors_block",
 *   admin_label = @Translation("Diamond Sponsors Block"),
 * )
 */
class DiamondSponsorsBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
    $sponsor_number = DiamondSponsorsConfigurationsForm::SPONSOR_NUMBER;

    $sponsors = [];
    for ($i = 1; $i <= $sponsor_number; $i++) {
      $sponsor_link = $this->state->get("dct_diamond_sponsors.sponsor_{$i}.link_url");
      $sponsor_image = $this->state->get("dct_diamond_sponsors.sponsor_{$i}.image");
      if ($sponsor_link && $sponsor_image) {
        $sponsor['link'] = $sponsor_link;
        $sponsor_image = File::load($sponsor_image);
        $sponsor['image'] = file_create_url($sponsor_image->getFileUri());
        $sponsors[] = $sponsor;
      }
    }

    return [
      '#theme' => 'dct_diamond_sponsors_block',
      '#sponsors' => $sponsors,
      '#cache' => [
        'tags' => [
          'dct_diamond_sponsors.block_information'
        ]
      ]
    ];
  }

}
