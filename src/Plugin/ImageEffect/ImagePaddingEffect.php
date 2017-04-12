<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageEffect\ImagePaddingEffect.
 */

namespace Drupal\iek\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * IEK - Padding.
 *
 * @ImageEffect(
 *   id = "iek_image_padding",
 *   label = @Translation("IEK - Padding"),
 *   description = @Translation("Add padding to an image.")
 * )
 */
class ImagePaddingEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('iek_image_padding', $this->configuration)) {
      $this->logger->error('IEK - Padding failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', [
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
    $dimensions['padding_top'] = $this->configuration['padding_top'];
    $dimensions['padding_right'] = $this->configuration['padding_right'];
    $dimensions['padding_bottom'] = $this->configuration['padding_bottom'];
    $dimensions['padding_left'] = $this->configuration['padding_left'];
    $dimensions['bg_color'] = $this->configuration['bg_color'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $data = $this->configuration;

    $summary = [
      '#theme' => 'iek_image_padding_summary',
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
      'padding_top'    => 10,
      'padding_right'  => 10,
      'padding_bottom' => 10,
      'padding_left'   => 10,
      'bg_color'       => '#FFFFFF',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
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

    $form['bg_color'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Background color'),
      '#default_value' => $this->configuration['bg_color'],
      '#size'          => 10,
      '#maxlength'     => 7,
      '#required'      => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['padding_top'] = $form_state->getValue('padding_top');
    $this->configuration['padding_right'] = $form_state->getValue('padding_right');
    $this->configuration['padding_bottom'] = $form_state->getValue('padding_bottom');
    $this->configuration['padding_left'] = $form_state->getValue('padding_left');

    $this->configuration['bg_color'] = $form_state->getValue('bg_color');
  }

}
