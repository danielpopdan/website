<?php

namespace Drupal\dct_commerce;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class PromoPriceCalculator.
 *
 * @package Drupal\dct_commerce
 */
class PromoPriceCalculator {

  /**
   * PromoPriceCalculator constructor.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * @param \Drupal\commerce\PurchasableEntityInterface $entity
   */
  public function calculatePromotionalPrice(PurchasableEntityInterface $entity, $store, $quantity = 1) {
    $order_item = $this->entityTypeManager->getStorage('commerce_order_item')->createFromPurchasableEntity($entity, ['quantity' => $quantity]);
    $order_item->save();
    $order = $this->entityTypeManager->getStorage('commerce_order')->create([
      'type' => 'dct_order',
      'state' => 'completed',
      'uid' => \Drupal::currentUser()->id(),
      'order_items' => [$order_item],
    ]);
    $order->setRefreshState(Order::REFRESH_ON_SAVE);
    $order->setStore($store);
    $order->save();
    $items = $order->getItems();
    $order_item = reset($items);
    $adjustments = $order_item->getAdjustments();
    $promotion_adjustments = array_filter($adjustments, function ($adjustment) {
      return $adjustment->getType() == 'promotion';
    });
    $unit_price = $order_item->getUnitPrice();
    foreach ($promotion_adjustments as $adjustment) {
      $unit_price = $unit_price->add($adjustment->getAmount());
    }
    $order_item->delete();
    $order->delete();

    return [
      'price' => $unit_price,
      'promotions' => $promotion_adjustments,
    ];
  }

}
