<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageEffect\ImageResizeEffect.
 */

namespace Drupal\iek\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * IEK - Resize.
 *
 * @ImageEffect(
 *   id = "iek_image_resize",
 *   label = @Translation("IEK - Resize"),
 *   description = @Translation("Resize an image by using the GD toolkit.")
 * )
 */
class ImageResizeEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('iek_image_resize', $this->configuration)) {
      $this->logger->error('IEK - Resize failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', [
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
    $dimensions['width'] = $this->configuration['width'];
    $dimensions['height'] = $this->configuration['height'];
    $dimensions['blank_margin'] = $this->configuration['blank_margin'];
    $dimensions['blank_margin_bg_color'] = $this->configuration['blank_margin_bg_color'];
    $dimensions['position'] = $this->configuration['position'];
    $dimensions['x'] = $this->configuration['x'];
    $dimensions['y'] = $this->configuration['y'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $data = $this->configuration;

    if ($data['blank_margin'] == 1) {
      $data['blank_margin'] = $this->t('Yes');
    }
    else {
      $data['blank_margin'] = $this->t('No');
    }

    $positions = $this->getAlignPositions();

    $data['position'] = $positions[$data['position']];

    $summary = [
      '#theme' => 'iek_image_resize_summary',
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
      'width'                 => NULL,
      'height'                => NULL,
      'blank_margin'          => 0,
      'blank_margin_bg_color' => '#FFFFFF',
      'position'              => 'middle_center',
      'x'                     => 0,
      'y'                     => 0,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['width'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Width'),
      '#default_value' => $this->configuration['width'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['height'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Height'),
      '#default_value' => $this->configuration['height'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
      '#min'           => 1,
    ];

    $form['blank_margin'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Blank margin'),
      '#default_value' => $this->configuration['blank_margin'],
      '#options'       => [
        1 => $this->t('Yes'),
        0 => $this->t('No'),
      ],
      '#required'      => TRUE,
    ];

    $form['blank_margin_bg_color'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Blank margin background color'),
      '#default_value' => $this->configuration['blank_margin_bg_color'],
      '#size'          => 10,
      '#maxlength'     => 7,
      '#states'        => [
        'visible' => [
          ':input[name="data[blank_margin]"]' => [
            'value' => 1,
          ],
        ],
      ],
    ];

    $form['position'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Align position'),
      '#default_value' => $this->configuration['position'],
      '#options'       => $this->getAlignPositions(),
      '#required'      => TRUE,
    ];

    $form['x'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('X-axis'),
      '#default_value' => $this->configuration['x'],
      '#required'      => TRUE,
      '#size'          => 5,
      '#states'        => [
        'visible' => [
          ':input[name="data[position]"]' => [
            'value' => 'coordinate',
          ],
        ],
      ],
    ];

    $form['y'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Y-axis'),
      '#default_value' => $this->configuration['y'],
      '#required'      => TRUE,
      '#size'          => 5,
      '#states'        => [
        'visible' => [
          ':input[name="data[position]"]' => [
            'value' => 'coordinate',
          ],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['width'] = $form_state->getValue('width');
    $this->configuration['height'] = $form_state->getValue('height');

    $this->configuration['blank_margin'] = $form_state->getValue('blank_margin');
    $this->configuration['blank_margin_bg_color'] = $form_state->getValue('blank_margin_bg_color');

    $this->configuration['position'] = $form_state->getValue('position');

    $this->configuration['x'] = $form_state->getValue('x');
    $this->configuration['y'] = $form_state->getValue('y');
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
