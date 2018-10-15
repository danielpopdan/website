<?php

namespace Drupal\dct_paragraphs\Plugin\ViewBuilder;

use Drupal\Core\Render\Element;
use Drupal\entity_view_builder\EntityViewBuilderBase;

/**
 * Definition for image_slider view builder alter.
 *
 * @EntityViewBuilder(
 *   id = "image_slider_view_builder",
 *   bundle = "image_slider_paragraph"
 * )
 */
class ImageSliderViewBuilder extends EntityViewBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function build(array &$build) {
    // Array containing the ids of the renderable items of the element.
    $children = Element::children($build['field_image']);
    $slider = [];

    // Adds attributes to the items of the element.
    foreach ($children as $child) {
      $slider[$child] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'location-slider--slide',
          ],
        ],
        'child' => $build['field_image'][$child],
      ];
      $slider[$child]['child']['#item_attributes']['data-action'][] = 'zoom';
    }
    $build['field_image'] = $slider;

  }

}
