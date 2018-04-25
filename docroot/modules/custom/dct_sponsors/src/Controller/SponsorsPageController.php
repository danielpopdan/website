<?php

namespace Drupal\dct_sponsors\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SponsorsPageController.
 *
 * @package Drupal\dct_sponsors\Controller
 */
class SponsorsPageController extends ControllerBase {

  /**
   * SponsorsPageController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns the sponsors page.
   *
   * @return array
   *   The sponsors page render array.
   */
  public function content() {
    // Load the sponsors.
    $sponsors = $this->entityTypeManager->getStorage('node')
      ->loadByProperties([
        'type' => 'sponsor',
        'status' => NodeInterface::PUBLISHED
      ]);
    // Group the sponsors by type and get the render array.
    $grouped_sponsors = [];
    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    $new_row = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'l-row'
        ]
      ],
      'sponsors' => [],
    ];
    foreach ($sponsors as $sponsor) {
      $type = $sponsor->get('field_type')->value;
      $sponsor = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'l-col',
            'l-col-2',
          ],
        ],
        'sponsor' => $view_builder->view($sponsor, 'teaser'),
      ];
      if ($type != 'diamond') {
        $sponsor['#attributes']['class'][] = 'l-col-md-4';
      }
      if (count($grouped_sponsors[$type]['first']) < 4) {
        if (empty($grouped_sponsors[$type]['first'])) {
          $grouped_sponsors[$type]['first'] = $new_row;
        }
        $grouped_sponsors[$type]['first']['sponsors'][] = $sponsor;
      }
      else {
        if (empty($grouped_sponsors[$type]['second'])) {
          $grouped_sponsors[$type]['second'] = $new_row;
        }
        $grouped_sponsors[$type]['second']['sponsors'][] = $sponsor;
      }
    }

    return [
      '#theme' => 'dct_sponsors_page',
      '#diamond' => (!empty($grouped_sponsors['diamond'])) ? $grouped_sponsors['diamond'] : NULL,
      '#gold' => (!empty($grouped_sponsors['gold'])) ? $grouped_sponsors['gold'] : NULL,
      '#silver' => (!empty($grouped_sponsors['silver'])) ? $grouped_sponsors['silver'] : NULL,
      '#community' => (!empty($grouped_sponsors['community'])) ? $grouped_sponsors['community'] : NULL,
      '#network' => (!empty($grouped_sponsors['network'])) ? $grouped_sponsors['network'] : NULL,
      '#media' => (!empty($grouped_sponsors['media'])) ? $grouped_sponsors['media'] : NULL,
    ];
  }

}
