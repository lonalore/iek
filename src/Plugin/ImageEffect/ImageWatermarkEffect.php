<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageEffect\ImageWatermarkEffect.
 */

namespace Drupal\iek\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * IEK - Watermark.
 *
 * @ImageEffect(
 *   id = "iek_image_watermark",
 *   label = @Translation("IEK - Watermark"),
 *   description = @Translation("Add a watermark text on an image.")
 * )
 */
class ImageWatermarkEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('iek_image_watermark', $this->configuration)) {
      $this->logger->error('IEK - Watermark failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', [
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
    $dimensions['text'] = $this->configuration['text'];
    $dimensions['font'] = $this->configuration['font'];
    $dimensions['color'] = $this->configuration['color'];
    $dimensions['size'] = $this->configuration['size'];
    $dimensions['angle'] = $this->configuration['angle'];
    $dimensions['position'] = $this->configuration['position'];

    $dimensions['padding_top'] = $this->configuration['padding_top'];
    $dimensions['padding_right'] = $this->configuration['padding_right'];
    $dimensions['padding_bottom'] = $this->configuration['padding_bottom'];
    $dimensions['padding_left'] = $this->configuration['padding_left'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $data = $this->configuration;

    $positions = $this->getAlignPositions();

    $data['position'] = $positions[$data['position']];

    $summary = [
      '#theme' => 'iek_image_watermark_summary',
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
      'text'           => '',
      'font'           => '',
      'color'          => '#FFFFFF',
      'size'           => 24,
      'angle'          => -45,
      'position'       => 'middle_center',
      'padding_top'    => 10,
      'padding_right'  => 10,
      'padding_bottom' => 10,
      'padding_left'   => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $iek_fonts = iek_get_watermark_fonts();
    $fonts = array();

    foreach ($iek_fonts as $item) {
      $fonts[$item['name']] = $item['title'];
    }

    $form['text'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Text'),
      '#default_value' => $this->configuration['text'],
      '#required'      => TRUE,
    ];

    $form['font'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Font'),
      '#default_value' => $this->configuration['font'],
      '#options'       => $fonts,
      '#required'      => TRUE,
    ];

    $form['color'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Color'),
      '#default_value' => $this->configuration['color'],
      '#size'          => 10,
      '#maxlength'     => 7,
      '#required'      => TRUE,
    ];

    $form['size'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Size'),
      '#default_value' => $this->configuration['size'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['angle'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Angle'),
      '#default_value' => $this->configuration['angle'],
      '#field_suffix'  => ' %',
      '#required'      => TRUE,
    ];

    $form['position'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Position'),
      '#default_value' => $this->configuration['position'],
      '#options'       => $this->getAlignPositions(),
    ];

    $form['padding_top'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Padding top'),
      '#default_value' => $this->configuration['padding_top'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['padding_right'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Padding right'),
      '#default_value' => $this->configuration['padding_right'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['padding_bottom'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Padding bottom'),
      '#default_value' => $this->configuration['padding_bottom'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['padding_left'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Padding left'),
      '#default_value' => $this->configuration['padding_left'],
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

    $this->configuration['text'] = $form_state->getValue('text');
    $this->configuration['font'] = $form_state->getValue('font');
    $this->configuration['color'] = $form_state->getValue('color');
    $this->configuration['size'] = $form_state->getValue('size');
    $this->configuration['angle'] = $form_state->getValue('angle');
    $this->configuration['position'] = $form_state->getValue('position');

    $this->configuration['padding_top'] = $form_state->getValue('padding_top');
    $this->configuration['padding_right'] = $form_state->getValue('padding_right');
    $this->configuration['padding_bottom'] = $form_state->getValue('padding_bottom');
    $this->configuration['padding_left'] = $form_state->getValue('padding_left');
  }

  private function getAlignPositions() {
    return [
      'top_left'      => $this->t('Top Left'),
      'top_center'    => $this->t('Top Center'),
      'top_right'     => $this->t('Top Right'),
      'middle_left'   => $this->t('Middle Left'),
      'middle_center' => $this->t('Middle Center'),
      'middle_right'  => $this->t('Middle Right'),
      'bottom_left'   => $this->t('Bottom Left'),
      'bottom_center' => $this->t('Bottom Center'),
      'bottom_right'  => $this->t('Bottom Right'),
      'coordinate'    => $this->t('Coordinate'),
    ];
  }

}
