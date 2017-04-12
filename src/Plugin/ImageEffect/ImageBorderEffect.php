<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageEffect\ImageBorderEffect.
 */

namespace Drupal\iek\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * IEK - Resize.
 *
 * @ImageEffect(
 *   id = "iek_image_border",
 *   label = @Translation("IEK - Border"),
 *   description = @Translation("Add border to an image.")
 * )
 */
class ImageBorderEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('iek_image_border', $this->configuration)) {
      $this->logger->error('IEK - Border failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', [
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
    $dimensions['border_color'] = $this->configuration['border_color'];
    $dimensions['border_thick_top'] = $this->configuration['border_thick_top'];
    $dimensions['border_thick_right'] = $this->configuration['border_thick_right'];
    $dimensions['border_thick_bottom'] = $this->configuration['border_thick_bottom'];
    $dimensions['border_thick_left'] = $this->configuration['border_thick_left'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $data = $this->configuration;

    $summary = [
      '#theme' => 'iek_image_border_summary',
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
      'border_color'        => '#EDEDED',
      'border_thick_top'    => 5,
      'border_thick_right'  => 5,
      'border_thick_bottom' => 5,
      'border_thick_left'   => 5,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['border_color'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Border color'),
      '#default_value' => $this->configuration['border_color'],
      '#size'          => 10,
      '#maxlength'     => 7,
      '#required'      => TRUE,
    ];

    $form['border_thick_top'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Border thick - top'),
      '#default_value' => $this->configuration['border_thick_top'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['border_thick_right'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Border thick - right'),
      '#default_value' => $this->configuration['border_thick_right'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['border_thick_bottom'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Border thick - bottom'),
      '#default_value' => $this->configuration['border_thick_bottom'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['border_thick_left'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Border thick - left'),
      '#default_value' => $this->configuration['border_thick_left'],
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

    $this->configuration['border_color'] = $form_state->getValue('border_color');

    $this->configuration['border_thick_top'] = $form_state->getValue('border_thick_top');
    $this->configuration['border_thick_right'] = $form_state->getValue('border_thick_right');
    $this->configuration['border_thick_bottom'] = $form_state->getValue('border_thick_bottom');
    $this->configuration['border_thick_left'] = $form_state->getValue('border_thick_left');
  }

}
