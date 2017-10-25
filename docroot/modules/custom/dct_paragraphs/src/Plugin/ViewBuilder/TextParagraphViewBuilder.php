<?php

namespace Drupal\dct_paragraphs\Plugin\ViewBuilder;

use Drupal\entity_view_builder\EntityViewBuilderBase;

/**
 * Definition for text_paragraph view builder alter.
 *
 * @EntityViewBuilder(
 *   id = "text_paragraph_view_builder",
 *   bundle = "text_paragraph"
 * )
 */
class TextParagraphViewBuilder extends EntityViewBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function build(array &$build) {
    $build['field_title'] = [
      '#prefix' => '<h2 class="small-title-block--title">',
      '#markup' => $build['#paragraph']->get('field_title')->value,
      '#suffix' => '</h2>',
    ];

    $build['field_text'] = [
      '#prefix' => '<div class="small-title-block--text">',
      '#markup' => $build['#paragraph']->get('field_text')->value,
      '#suffix' => '</div>',
    ];
  }

}
