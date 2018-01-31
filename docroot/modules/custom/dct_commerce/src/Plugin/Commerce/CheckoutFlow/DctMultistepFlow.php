<?php

namespace Drupal\dct_commerce\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;

/**
 * Provides the DCT multistep checkout flow.
 *
 * @CommerceCheckoutFlow(
 *   id = "dct_multistep",
 *   label = "DCT Multistep Flow",
 * )
 */
class DctMultistepFlow extends CheckoutFlowWithPanesBase {

  /**
   * {@inheritdoc}
   */
  public function getSteps() {
    // Note that previous_label and next_label are not the labels
    // shown on the step itself. Instead, they are the labels shown
    // when going back to the step, or proceeding to the step.
    return [
      'login' => [
        'label' => $this->t('Login'),
        'previous_label' => $this->t('Go back'),
        'has_sidebar' => FALSE,
      ],
      'ticket_information' => [
        'label' => $this->t('Ticket information'),
        'has_sidebar' => TRUE,
        'previous_label' => $this->t('Go back'),
      ],
      'order_information' => [
        'label' => $this->t('Order information'),
        'has_sidebar' => TRUE,
        'previous_label' => $this->t('Go back'),
        'next_label' => $this->t('Continue to order information'),
      ],
      'review' => [
        'label' => $this->t('Review'),
        'next_label' => $this->t('Continue to review'),
        'previous_label' => $this->t('Go back'),
        'has_sidebar' => TRUE,
      ],
    ] + parent::getSteps();
  }

}
