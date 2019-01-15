<?php

namespace Drupal\dct_commerce\Plugin\ConfirmationPage;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Url;
use Drupal\dct_confirmation\ConfirmationPageInterface;

/**
 * ForgotPasswordConfirmationPlugin.
 *
 * @ConfirmationPage(
 *   id = "dct_commerce_ticket_redemption_form_confirmation",
 *   form_id = "dct_commerce_ticket_redemption_form"
 * )
 */
class RedemptionConfirmationPlugin extends PluginBase implements ConfirmationPageInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return t('Redeem a coupon');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('You successfully redeemed your coupon. You can see your redeemed coupon on your profile page.');
  }

  /**
   * {@inheritdoc}
   */
  public function getLink() {
    return [
      'title' => $this->t('Check your ticket'),
      'url' => Url::fromRoute('dct_commerce.my_ticket'),
    ];
  }

}
