<?php

namespace Drupal\dct_commerce\Plugin\Field\FieldFormatter;

use Drupal\address\Plugin\Field\FieldFormatter\AddressDefaultFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Plugin implementation of the 'address_dct' formatter.
 *
 * @FieldFormatter(
 *   id = "address_dct",
 *   label = @Translation("DCT custom"),
 *   field_types = {
 *     "address",
 *   },
 * )
 */
class DctAddressFormatter extends AddressDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#prefix' => '<div class="address" translate="no">',
        '#suffix' => '</div>',
        '#post_render' => [
          [get_class($this), 'postRender'],
        ],
        '#cache' => [
          'contexts' => [
            'languages:' . LanguageInterface::TYPE_INTERFACE,
          ],
        ],
      ];
      $elements[$delta] += $this->viewElement($item, $langcode);
    }

    return $elements;
  }

}
