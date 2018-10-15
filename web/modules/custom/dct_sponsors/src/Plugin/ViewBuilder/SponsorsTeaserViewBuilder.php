<?php

namespace Drupal\dct_sponsors\Plugin\ViewBuilder;

use Drupal\entity_view_builder\EntityViewBuilderBase;

/**
 * Definition for benefits_group view builder alter.
 *
 * @EntityViewBuilder(
 *   id = "sponsors_teaser_view_builder",
 *   bundle = "sponsor"
 * )
 */
class SponsorsTeaserViewBuilder extends EntityViewBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function build(array &$build) {
    $build['field_external_link'][0]['#options']['attributes']['class'][] = 'button';
    $build['field_external_link'][0]['#options']['attributes']['class'][] = 'button--default';
  }

}
