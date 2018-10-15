<?php

namespace Drupal\dct_paragraphs\Plugin\ViewBuilder;

use Drupal\entity_view_builder\EntityViewBuilderBase;

/**
 * Definition for benefits_group view builder alter.
 *
 * @EntityViewBuilder(
 *   id = "benefits_group_view_builder",
 *   bundle = "benefits_group"
 * )
 */
class BenefitsGroupViewBuilder extends EntityViewBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function build(array &$build) {
    $benefits = $this->getEntity()->get('field_benefit');
    $output = [];

    // All user input is added to $output, together with the separator.
    foreach ($benefits as $index => $benefit) {
      $output[] = $benefit->getValue()['value'];

      // Between the benefits we place the '|' character.
      if ($index != $benefits->count() - 1) {
        $output[] = '|';
      }
    }

    $build['field_benefit'] = $output;
  }

}
