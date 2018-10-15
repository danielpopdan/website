<?php

namespace Drupal\dct_paragraphs\Plugin\ViewBuilder;

use Drupal\entity_view_builder\EntityViewBuilderBase;

/**
 * Definition for sponsorship_container view builder alter.
 *
 * @EntityViewBuilder(
 *   id = "sponsorship_container_view_builder",
 *   bundle = "sponsorship_container"
 * )
 */
class SponsorshipContainerViewBuilder extends EntityViewBuilderBase {

  /**
   * {@inheritdoc}
   */
  public function build(array &$build) {
    $rows = $this->getBoxesInRows($build['field_sponsorship_item']);

    foreach ($rows as $index => $row) {
      $rows[$index]['#type'] = 'container';
      $rows[$index]['#attributes']['class'][] = 'l-row';
    }

    $build['field_sponsorship_item'] = $rows;
    $build['#attached']['library'][] = 'dct_theme/matchHeight';
  }

  /**
   * Creates a nested array containing rows of boxes.
   *
   * @param array $boxes
   *   The array of boxes.
   *
   * @return array
   *   The nested array containing rows of boxes based on the selected layout.
   */
  protected function getBoxesInRows(array $boxes) {
    // Needed empty arrays are initialized.
    $output = [];
    $row = 0;

    // The box_container paragraph entity is retrieved.
    $paragraph = $this->getEntity();

    // The box list on the entity is retrieved.
    $entities = $paragraph->get('field_sponsorship_item')->getValue();

    foreach ($entities as $key => $value) {
      $output[$row][] = $boxes[$key];

      if ($key % 2 != 0) {
        $row++;
      }
    }

    return $output;
  }

}
