<?php

/**
 * @file
 * Contains \Drupal\iek\Plugin\ImageToolkit\Operation\gd\ImageOverlay.
 */

namespace Drupal\iek\Plugin\ImageToolkit\Operation\gd;

use Drupal\system\Plugin\ImageToolkit\Operation\gd\GDImageToolkitOperationBase;

/**
 * Defines IEK - Overlay operation.
 *
 * @ImageToolkitOperation(
 *   id = "gd_iek_image_overlay",
 *   toolkit = "gd",
 *   operation = "iek_image_overlay",
 *   label = @Translation("IEK - Overlay"),
 *   description = @Translation("Apply an overlay to an image.")
 * )
 */
class ImageOverlay extends GDImageToolkitOperationBase {

  /**
   * {@inheritdoc}
   */
  protected function arguments() {
    return [
      'overlay_name' => [
        'description' => 'Overlay name',
      ],
      'overlay_offset' => [
        'description' => 'Overlay offset',
      ],
      'bg_offset' => [
        'description' => 'Background offset',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function validateArguments(array $arguments) {
    // Fail when overlay offset is negative.
    if ((int) $arguments['overlay_offset'] < 0) {
      throw new \InvalidArgumentException("Invalid overlay offset ('{$arguments['overlay_offset']}') specified for the image 'iek_image_overlay' operation");
    }

    // Fail when bg offset is negative.
    if ((int) $arguments['bg_offset'] < 0) {
      throw new \InvalidArgumentException("Invalid bg offset ('{$arguments['bg_offset']}') specified for the image 'iek_image_overlay' operation");
    }

    return $arguments;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(array $arguments = []) {
    $data = $arguments;

    $iek_overlay = iek_get_overlays($data['overlay_name']);
    $overlay_path = \Drupal::service('file_system')
      ->realpath($iek_overlay['path'] . '/' . $iek_overlay['file']);

    $overlay_info = iek_image_get_info($overlay_path);
    $overlay_tmp = iek_gd_create_image($overlay_path);

    imagecopyresampled($this->getToolkit()->getResource(),
      $overlay_tmp,
      $data['bg_offset'],
      $data['bg_offset'],
      $data['overlay_offset'],
      $data['overlay_offset'],
      $this->getToolkit()->getWidth() - ($data['bg_offset'] * 2),
      $this->getToolkit()->getHeight() - ($data['bg_offset'] * 2),
      $overlay_info['width'] - ($data['overlay_offset'] * 2),
      $overlay_info['height'] - ($data['overlay_offset'] * 2)
    );

    return TRUE;
  }

}
