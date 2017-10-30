<?php

namespace Drupal\dct_paragraphs\Plugin\ViewBuilder;

use Drupal\entity_view_builder\EntityViewBuilderBase;

/**
 * Definition for social share paragraph.
 *
 * @EntityViewBuilder(
 *   id = "social_share_view_builder",
 *   bundle = "social_share"
 * )
 */
class SocialShareViewBuilder extends EntityViewBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function build(array &$build) {
    $build = [
      '#addtoany_html' => addtoany_create_node_buttons(NULL),
      '#theme' => 'addtoany_standard',
      '#cache' => [
        'contexts' => ['url'],
      ],
    ];
  }

}
