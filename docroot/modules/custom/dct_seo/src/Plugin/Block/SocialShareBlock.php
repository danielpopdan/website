<?php

namespace Drupal\dct_seo\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides Social Share Block.
 *
 * @Block(
 *   id = "social_share",
 *   admin_label = @Translation("Social Share Block"),
 * )
 */
class SocialShareBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#theme' => 'dct_social_share_block',
    ];
  }

}
