<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageEffect\ImageFilterEffect.
 */

namespace Drupal\iek\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * IEK - Filter.
 *
 * @ImageEffect(
 *   id = "iek_image_filter",
 *   label = @Translation("IEK - Filter"),
 *   description = @Translation("Apply a filter to an image.")
 * )
 */
class ImageFilterEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('iek_image_filter', $this->configuration)) {
      $this->logger->error('IEK - Filter failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', [
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
    $dimensions['filter_name'] = $this->configuration['filter_name'];
    $dimensions['repeat'] = $this->configuration['repeat'];
    $dimensions['arg1'] = $this->configuration['arg1'];
    $dimensions['arg2'] = $this->configuration['arg2'];
    $dimensions['arg3'] = $this->configuration['arg3'];
    $dimensions['arg4'] = $this->configuration['arg4'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $data = $this->configuration;

    $filters = iek_get_image_filters();

    $data['filter_name'] = $filters[$data['filter_name']];

    $summary = [
      '#theme' => 'iek_image_filter_summary',
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
      'filter_name' => '',
      'repeat'      => 1,
      'arg1'        => 0,
      'arg2'        => 0,
      'arg3'        => 0,
      'arg4'        => 0,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $filters = iek_get_image_filters();

    $int127 = [];
    $int255 = [];
    for ($i = 0; $i <= 255; $i++) {
      if ($i <= 127) {
        $int127[$i] = $i;
      }
      $int255[$i] = $i;
    }

    $form['filter_name'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Filter name'),
      '#default_value' => $this->configuration['filter_name'],
      '#options'       => $filters,
      '#required'      => TRUE,
    ];

    $form['repeat'] = [
      '#type'          => 'select',
      '#options'       => $int255,
      '#title'         => $this->t('Repeat'),
      '#description'   => $this->t('How many times do you want to repeat the filter action? Caution: higher repeat times will cause your site performance issue.'),
      '#default_value' => $this->configuration['repeat'],
      '#required'      => TRUE,
    ];

    $form['arg1'] = [
      '#type'          => 'select',
      '#options'       => $int255,
      '#title'         => $this->t('arg1'),
      '#default_value' => $this->configuration['arg1'],
      '#required'      => TRUE,
      '#states'        => [
        'visible' => [
          ':input[name="data[filter_name]"]' => [
            ['value' => IMG_FILTER_BRIGHTNESS],
            ['value' => IMG_FILTER_CONTRAST],
            ['value' => IMG_FILTER_COLORIZE],
            ['value' => IMG_FILTER_SMOOTH],
            ['value' => IMG_FILTER_PIXELATE],
          ],
        ],
      ],
    ];

    $form['arg2'] = [
      '#type'          => 'select',
      '#options'       => $int255,
      '#title'         => $this->t('arg2'),
      '#default_value' => $this->configuration['arg2'],
      '#required'      => TRUE,
      '#states'        => [
        'visible' => [
          ':input[name="data[filter_name]"]' => [
            ['value' => IMG_FILTER_COLORIZE],
            ['value' => IMG_FILTER_PIXELATE],
          ],
        ],
      ],
    ];

    $form['arg3'] = [
      '#type'          => 'select',
      '#options'       => $int255,
      '#title'         => $this->t('arg3'),
      '#default_value' => $this->configuration['arg3'],
      '#required'      => TRUE,
      '#states'        => [
        'visible' => [
          ':input[name="data[filter_name]"]' => [
            ['value' => IMG_FILTER_COLORIZE],
          ],
        ],
      ],
    ];

    $form['arg4'] = [
      '#type'          => 'select',
      '#options'       => $int127,
      '#title'         => $this->t('arg4'),
      '#default_value' => $this->configuration['arg4'],
      '#required'      => TRUE,
      '#states'        => [
        'visible' => [
          ':input[name="data[filter_name]"]' => [
            ['value' => IMG_FILTER_COLORIZE],
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

    $this->configuration['filter_name'] = $form_state->getValue('filter_name');
    $this->configuration['repeat'] = $form_state->getValue('repeat');

    $this->configuration['arg1'] = $form_state->getValue('arg1');
    $this->configuration['arg2'] = $form_state->getValue('arg2');
    $this->configuration['arg3'] = $form_state->getValue('arg3');
    $this->configuration['arg4'] = $form_state->getValue('arg4');
  }

}
