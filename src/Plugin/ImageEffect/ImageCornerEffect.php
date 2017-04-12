<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageEffect\ImageCornerEffect.
 */

namespace Drupal\iek\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * IEK - Corner.
 *
 * @ImageEffect(
 *   id = "iek_image_corner",
 *   label = @Translation("IEK - Corner"),
 *   description = @Translation("Add rounded corner to an image.")
 * )
 */
class ImageCornerEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('iek_image_corner', $this->configuration)) {
      $this->logger->error('IEK - Corner failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', [
        '%toolkit'    => $image->getToolkitId(),
        '%path'       => $image->getSource(),
        '%mimetype'   => $image->getMimeType(),
        '%dimensions' => $image->getWidth() . 'x' . $image->getHeight()
      ]);

      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function transformDimensions(array &$dimensions, $uri) {
    $dimensions['radius'] = $this->configuration['radius'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $data = $this->configuration;

    $summary = [
      '#theme' => 'iek_image_corner_summary',
      '#data'  => $data,
    ];

    $summary += parent::getSummary();

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'radius' => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['radius'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Radius'),
      '#default_value' => $this->configuration['radius'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['radius'] = $form_state->getValue('radius');
  }

}
