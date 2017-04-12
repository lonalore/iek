<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageEffect\ImageOverlayEffect.
 */

namespace Drupal\iek\Plugin\ImageEffect;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Image\ImageInterface;
use Drupal\image\ConfigurableImageEffectBase;

/**
 * IEK - Overlay.
 *
 * @ImageEffect(
 *   id = "iek_image_overlay",
 *   label = @Translation("IEK - Overlay"),
 *   description = @Translation("Apply an overlay to an image.")
 * )
 */
class ImageOverlayEffect extends ConfigurableImageEffectBase {

  /**
   * {@inheritdoc}
   */
  public function applyEffect(ImageInterface $image) {
    if (!$image->apply('iek_image_overlay', $this->configuration)) {
      $this->logger->error('IEK - Overlay failed using the %toolkit toolkit on %path (%mimetype, %dimensions)', [
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
    $dimensions['overlay_name'] = $this->configuration['overlay_name'];
    $dimensions['overlay_offset'] = $this->configuration['overlay_offset'];
    $dimensions['bg_offset'] = $this->configuration['bg_offset'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $data = $this->configuration;

    $iek_overlays = iek_get_overlays();

    foreach ($iek_overlays as $item1) {
      foreach ($item1['children'] as $item2) {
        if ($item2['name'] == $data['overlay_name']) {
          $data['overlay_name'] = $item2['title'];
        }
      }
    }

    $summary = [
      '#theme' => 'iek_image_overlay_summary',
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
      'overlay_name'   => '',
      'overlay_offset' => 0,
      'bg_offset'      => 0,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $iek_overlays = iek_get_overlays();

    $overlay_names = [];

    foreach ($iek_overlays as $item1) {
      foreach ($item1['children'] as $item2) {
        $overlay_names[$item1['name']][$item2['name']] = $item2['title'];
      }
    }

    $form['overlay_name'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Overlay name'),
      '#default_value' => $this->configuration['overlay_name'],
      '#options'       => $overlay_names,
      '#required'      => TRUE,
    ];

    $form['overlay_offset'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Overlay offset'),
      '#default_value' => $this->configuration['overlay_offset'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
    ];

    $form['bg_offset'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Background offset'),
      '#default_value' => $this->configuration['bg_offset'],
      '#field_suffix'  => ' ' . $this->t('pixels'),
      '#required'      => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['overlay_name'] = $form_state->getValue('overlay_name');
    $this->configuration['overlay_offset'] = $form_state->getValue('overlay_offset');
    $this->configuration['bg_offset'] = $form_state->getValue('bg_offset');
  }

}
