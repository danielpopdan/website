<?php

namespace Drupal\dct_homepage\Form;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DiamondSponsorsConfigurationsForm.
 *
 * @package Drupal\dct_homepage\Form
 */
class DiamondSponsorsConfigurationsForm extends FormBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The cache_tags.invalidator service.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $cacheInvalidator;

  /**
   * The number of diamond sponsors.
   *
   * @var integer
   */
  const SPONSOR_NUMBER = 2;


  /**
   * {@inheritdoc}
   */
  public function __construct(StateInterface $state, CacheTagsInvalidatorInterface $cacheInvalidator) {
    $this->state = $state;
    $this->cacheInvalidator = $cacheInvalidator;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dct_diamond_sponsors_configurations';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    for ($i = 1; $i <= self::SPONSOR_NUMBER; $i++) {
      $form["sponsor_{$i}"] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Diamond sponsor @sponsor_number', ['@sponsor_number' => $i]),
        '#description' => $this->t('Set a url and an image for the Sponsor'),
      ];

      $form["sponsor_{$i}"]["link_url_{$i}"] = [
        '#type' => 'url',
        '#title' => $this->t('Url'),
        '#default_value' => $this->state->get("dct_diamond_sponsors.sponsor_{$i}.link_url"),
        '#maxlength' => 1024,
      ];

      $image_default = $this->state->get("dct_diamond_sponsors.sponsor_{$i}.image");
      $form["sponsor_{$i}"]["image_{$i}"] = [
        '#type' => 'managed_file',
        '#title' => t('Image'),
        '#upload_location' => 'public://images/',
        '#default_value' => $image_default ? [$image_default] : NULL,
        '#description' => t('This image is used in the footer to display diamond sponsors.'),
        '#upload_validators' => [
          'file_validate_is_image' => [],
          'file_validate_extensions' => ['png jpg jpeg'],
          'file_validate_size' => [25600000],
        ],
      ];
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configurations'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    for ($i = 1; $i <= self::SPONSOR_NUMBER; $i++) {
      $this->state->set("dct_diamond_sponsors.sponsor_{$i}.link_url", $form_state->getValue("link_url_{$i}"));
      $fid = $form_state->getValue("image_{$i}");
      if ($fid) {
        $file = File::load($fid[0]);
        $file->setPermanent();
        $file->save();
        $fid = $fid[0];
      }
      else {
        $fid = NULL;
      }
      $this->state->set("dct_diamond_sponsors.sponsor_{$i}.image", $fid);
    }

    // Invalidate the cache set on DiamondSponsorsBlock.
    $this->cacheInvalidator->invalidateTags(['dct_diamond_sponsors.block_information']);

    drupal_set_message($this->t('The settings have been successfully saved'));
  }

}
